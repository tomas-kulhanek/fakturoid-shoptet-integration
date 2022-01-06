<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\InvoiceRepository;
use App\DTO\Shoptet\Invoice\InvoiceResponse;
use App\Log\ActionLog;
use App\Savers\InvoiceSaver;

class InvoiceManager
{
	public function __construct(
		private EntityManager   $entityManager,
		private ClientInterface $shoptetClient,
		private InvoiceSaver    $invoiceSaver,
		private ActionLog       $actionLog
	) {
	}

	public function getRepository(): InvoiceRepository
	{
		/** @var InvoiceRepository $repository */
		$repository = $this->entityManager->getRepository(Invoice::class);
		return $repository;
	}

	public function synchronizeFromShoptet(Project $project, string $code): ?Invoice
	{
		$orderData = $this->shoptetClient->findInvoice($code, $project);
		if ($orderData->hasErrors()) {
			return null;
		}
		if (!$orderData->data instanceof InvoiceResponse) {
			return null;
		}
		bdump($orderData);
		$invoice = $this->invoiceSaver->save($project, $orderData->data->invoice);

		$this->actionLog->logInvoice($project, ActionLog::SHOPTET_INVOICE_DETAIL, $invoice);
		return $invoice;
	}

	public function find(Project $project, int $id): Invoice
	{
		return $this->getRepository()
			->createQueryBuilder('i')
			->addSelect('it')
			->addSelect('p')
			->addSelect('ba')
			->addSelect('da')
			->addSelect('o')
			->addSelect('ps')
			->leftJoin('i.order', 'o')
			->leftJoin('i.items', 'it')
			->leftJoin('i.billingAddress', 'ba')
			->leftJoin('i.deliveryAddress', 'da')
			->innerJoin('i.project', 'p')
			->innerJoin('p.settings', 'ps')
			->where('i.project = :project')
			->andWhere('i.id = :id')
			->setParameter('project', $project)
			->setParameter('id', $id)
			->getQuery()->getSingleResult();
	}

	public function findByShoptet(Project $project, string $shoptetCode): ?Invoice
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'shoptetCode' => $shoptetCode]);
	}
}
