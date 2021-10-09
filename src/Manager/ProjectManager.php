<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\Repository\Shoptet\ProjectRepository;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\Exception\Logic\NotFoundException;
use App\MessageBus\SynchronizeMessageBusDispatcher;
use App\Security\SecretVault\ISecretVault;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Nette\Http\Url;

class ProjectManager
{
	public function __construct(
		private ClientInterface                 $apiDispatcher,
		private EntityManagerInterface          $entityManager,
		private ISecretVault                    $secretVault,
		private EshopInfoManager                $eshopInfoManager,
		private WebhookManager                  $webhookManager,
		private SynchronizeMessageBusDispatcher $synchronizeMessageBusDispatcher
	) {
	}

	public function getRepository(): ProjectRepository
	{
		/** @var ProjectRepository $projectRepository */
		$projectRepository = $this->entityManager->getRepository(Project::class);
		return $projectRepository;
	}

	/**
	 * @param Project $project
	 * @param string $accountingAccount
	 * @param string $accountingEmail
	 * @param string $accountingApiKey
	 * @param string[] $synchronize
	 * @param int $automatization
	 */
	public function initializeProject(
		Project $project,
		string  $accountingAccount,
		string  $accountingEmail,
		string  $accountingApiKey,
		array   $synchronize,
		int     $automatization = ProjectSetting::AUTOMATIZATION_MANUAL
	): void {
		if ($project->isActive() || $project->isSuspended()) {
			return;
		}
		$settings = $project->getSettings();
		$settings->setAccountingAccount($accountingAccount);
		$settings->setAccountingEmail($accountingEmail);
		$settings->setAccountingApiKey(
			$this->secretVault->encrypt($accountingApiKey)
		);
		$settings->setAutomatization($automatization);

		$settings->setShoptetSynchronizeOrders(true);
		$webhooks = new WebhookRegistrationRequest();
		$this->webhookManager->registerMandatoryHooks($webhooks);
		$this->webhookManager->registerOrderHooks($webhooks);
		if (in_array('invoices', $synchronize, true)) {
			$settings->setShoptetSynchronizeInvoices(true);
			$this->webhookManager->registerInvoiceHooks($webhooks);
		}
		if (in_array('proformaInvoices', $synchronize, true)) {
			$settings->setShoptetSynchronizeProformaInvoices(true);
			$this->webhookManager->registerProformaInvoiceHooks($webhooks);
		}
		$this->webhookManager->registerHooks($webhooks, $project);
		$project->initialize();
		$this->entityManager->flush();
		$this->eshopInfoManager->syncOrderStatuses($project);

		$startDate = (new \DateTimeImmutable())->modify('-30 days');
		$this->synchronizeMessageBusDispatcher->dispatchCustomer($project, $startDate);
		$this->synchronizeMessageBusDispatcher->dispatchOrder($project, $startDate);
		if (in_array('invoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchInvoice($project, $startDate);
		}
		if (in_array('proformaInvoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchProformaInvoice($project, $startDate);
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
		$projects = $this->getRepository()
			->createQueryBuilder('p')
			->addSelect('ps')
			->leftJoin('p.setting', 'ps')
			->where('p.state = :state')
			->setParameter('state', Project::STATE_ACTIVE)
			->getQuery()->getArrayResult();

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
			->createQueryBuilder('p');
		try {
			$project = $qb
				->where($qb->expr()->like('p.eshopUrl', ':eshopUrl'))
				->orWhere($qb->expr()->like('p.eshopUrl', ':eshopUrl2'))
				->setParameter('eshopUrl', $url->getAbsoluteUrl())
				->setParameter('eshopUrl2', $clonedUrl->getAbsoluteUrl())
				->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw new NotFoundException('Eshop was not found');
		}
		return $project;
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

	public function confirmInstallation(string $code): Project
	{
		$installationData = $this->apiDispatcher->confirmInstallation($code);
		try {
			$project = $this->getByEshopId($installationData->eshopId);
		} catch (NotFoundException) {
			$project = new Project();
			$this->entityManager->persist($project);
			$projectSetting = new ProjectSetting($project);
			$this->entityManager->persist($projectSetting);
		}
		$project->setAccessToken(
			$this->secretVault->encrypt($installationData->access_token)
		);
		$project->setContactEmail($installationData->contactEmail);
		$project->setEshopId($installationData->eshopId);
		$project->setEshopUrl($installationData->eshopUrl);
		$project->setScope($installationData->scope);
		$project->setTokenType($installationData->token_type);

		$userEntity = new User(
			email: $installationData->contactEmail,
			project: $project
		);
		$userEntity->setRole(User::ROLE_OWNER);
		$this->entityManager->persist($userEntity);
		//todo zaslat jeste email
		$this->entityManager->flush();
		return $project;
	}
}
