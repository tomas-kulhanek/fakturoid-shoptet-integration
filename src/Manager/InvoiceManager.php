<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\InvoiceRepository;
use App\Savers\InvoiceSaver;

class InvoiceManager
{
	public function __construct(
		private EntityManager   $entityManager,
		private ClientInterface $shoptetClient,
		private InvoiceSaver    $invoiceSaver
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
		bdump($orderData);
		return $this->invoiceSaver->save($project, $orderData);
	}

	public function find(Project $project, int $id): Invoice
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'id' => $id]);
	}

	public function findByShoptet(Project $project, string $shoptetCode): ?Invoice
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'shoptetCode' => $shoptetCode]);
	}
}
