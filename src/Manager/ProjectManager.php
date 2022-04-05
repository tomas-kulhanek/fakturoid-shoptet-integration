<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\CustomerBillingAddress;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\Shoptet\ProjectRepository;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\Exception\Logic\NotFoundException;
use App\MessageBus\SynchronizeMessageBusDispatcher;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Nette\Http\Url;
use Symfony\Component\Messenger\MessageBusInterface;
use Tracy\Debugger;
use Tracy\ILogger;

class ProjectManager
{
	public function __construct(
		private ClientInterface                 $apiDispatcher,
		private EntityManagerInterface          $entityManager,
		private EshopInfoManager                $eshopInfoManager,
		private WebhookManager                  $webhookManager,
		private SynchronizeMessageBusDispatcher $synchronizeMessageBusDispatcher,
		private MessageBusInterface             $messageBus,
		private AccountingManager               $accountingManager
	) {
	}

	public function getRepository(): ProjectRepository
	{
		/** @var ProjectRepository $projectRepository */
		$projectRepository = $this->entityManager->getRepository(Project::class);
		return $projectRepository;
	}

	public function disableAutomatization(Project $project, int $httpCodeReason): void
	{
		// todo zaslat nejaky email s informaci proc k tomu doslo
		$project->getSettings()->setAutomatization(ProjectSetting::AUTOMATIZATION_MANUAL);
	}

	/**
	 * @param string[] $synchronize
	 */
	public function initializeProject(
		Project            $project,
		string             $accountingAccount,
		string             $accountingEmail,
		string             $accountingApiKey,
		int                $accountingNumberLineId,
		array              $synchronize,
		string             $customerName,
		\DateTimeImmutable $startDate,
		bool               $enableAccountingUpdate = true,
		int                $automatization = ProjectSetting::AUTOMATIZATION_MANUAL
	): void {
		if ($project->isActive() || $project->isSuspended()) {
			return;
		}
		$settings = $project->getSettings();
		$settings->setAccountingAccount($accountingAccount);
		$settings->setAccountingEmail($accountingEmail);
		$settings->setAccountingApiKey($accountingApiKey);
		$settings->setAccountingUpdate($enableAccountingUpdate);
		if ($accountingNumberLineId > 0) {
			$settings->setAccountingNumberLineId($accountingNumberLineId);
		} else {
			$settings->setAccountingNumberLineId(null);
		}
		$settings->setAutomatization($automatization);

		$settings->setShoptetSynchronizeOrders(false);
		$webhooks = new WebhookRegistrationRequest();
		$this->webhookManager->registerMandatoryHooks($webhooks, $project);
		//$this->webhookManager->registerOrderHooks($webhooks, $project);
		if (in_array('invoices', $synchronize, true)) {
			$settings->setShoptetSynchronizeInvoices(true);
			$this->webhookManager->registerInvoiceHooks($webhooks, $project);
		} else {
			$settings->setShoptetSynchronizeInvoices(false);
		}
		if (in_array('proformaInvoices', $synchronize, true) && $accountingNumberLineId < 1) {
			$settings->setShoptetSynchronizeProformaInvoices(true);
			$this->webhookManager->registerProformaInvoiceHooks($webhooks, $project);
		} else {
			$settings->setShoptetSynchronizeProformaInvoices(false);
		}
		if (in_array('creditNotes', $synchronize, true)) {
			$settings->setShoptetSynchronizeCreditNotes(true);
			$this->webhookManager->registerCreditNoteHooks($webhooks, $project);
		} else {
			$settings->setShoptetSynchronizeCreditNotes(false);
		}
		$this->webhookManager->registerHooks($webhooks, $project);
		$project->initialize();

		$endUser = new Customer($project);
		$endUser->setCreationTime(new \DateTimeImmutable());
		$endUser->setEndUser(true);
		$this->entityManager->persist($endUser);
		$endUser->setBillingAddress(new CustomerBillingAddress());
		$endUser->getBillingAddress()->setCustomer($endUser);
		$this->entityManager->persist($endUser->getBillingAddress());
		$endUser->getBillingAddress()->setFullName($customerName);

		$this->entityManager->flush();
		$this->accountingManager->syncBankAccounts($project);

		$this->eshopInfoManager->syncBaseData($project);

		$this->synchronizeMessageBusDispatcher->dispatchCustomer($project, $startDate);
		//$this->synchronizeMessageBusDispatcher->dispatchOrder($project, $startDate);
		if (in_array('proformaInvoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchProformaInvoice($project, $startDate);
		}
		if (in_array('invoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchInvoice($project, $startDate);
		}
		if (in_array('creditNotes', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchCreditNotes($project, $startDate);
		}
	}

	/**
	 * @return Collection<int, Project>
	 */
	public function getAllProjects(): Collection
	{
		$projects = $this->getRepository()
			->findAll();
		return new ArrayCollection($projects);
	}

	/**
	 * @return Collection<int, Project>
	 */
	public function getAllActiveProjects(): Collection
	{
		$projects = $this->getRepository()->findBy(['state' => Project::STATE_ACTIVE]);

		return new ArrayCollection($projects);
	}

	public function getByEshopUrl(string $eshopUrl): Project
	{
		$url = new Url();
		$url->setScheme('https');
		$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $eshopUrl));
		$clonedUrl = clone $url;
		$clonedUrl->setScheme('http');
		$qb = $this->entityManager->getRepository(Project::class)
			->createQueryBuilder('p')
			->innerJoin('p.users', 'pu')
			->addSelect('pu');
		try {
			/** @var Project $project */
			$project = $qb
				->where($qb->expr()->like('p.eshopUrl', ':eshopUrl'))
				->orWhere($qb->expr()->like('p.eshopUrl', ':eshopUrl2'))
				->setParameter('eshopUrl', $url->getAbsoluteUrl())
				->setParameter('eshopUrl2', $clonedUrl->getAbsoluteUrl())
				->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw new NotFoundException('Eshop was not found');
		}

		return  $this->getByEshopId($project->getEshopId());
	}

	public function getByEshopId(int $eshopId): Project
	{
		$project = $this->entityManager->getRepository(Project::class)
			->findOneBy(['eshopId' => $eshopId]);
		if (!$project instanceof Project) {
			throw new NotFoundException('Eshop was not found');
		}
		return $project;
	}

	public function confirmInstallation(string $code): void
	{
		$installationData = $this->apiDispatcher->confirmInstallation($code);

		$this->messageBus->dispatch($installationData);
	}

	public function renewSigningKey(Project $project): void
	{
		$response = $this->apiDispatcher->renewSignatureKey($project);
		if (!$response->hasErrors()) {
			$project->setSigningKey(
				$response->data->signatureKey
			);
			$this->entityManager->flush();
			return;
		}
		Debugger::log($response->errors, ILogger::CRITICAL);
	}
}
