<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\ProformaInvoiceRepository;
use App\Savers\ProformaInvoiceSaver;

class ProformaInvoiceManager
{
	public function __construct(
		private EntityManager $entityManager,
		private ProformaInvoiceSaver $invoiceSaver,
		private ClientInterface $shoptetClient
	) {
	}

	public function getRepository(): ProformaInvoiceRepository
	{
		/** @var ProformaInvoiceRepository $repository */
		$repository = $this->entityManager->getRepository(ProformaInvoice::class);
		return $repository;
	}

	public function synchronizeFromShoptet(Project $project, string $code): ?ProformaInvoice
	{
		$orderData = $this->shoptetClient->findProformaInvoice($code, $project);
		bdump($orderData);
		return $this->invoiceSaver->save($project, $orderData);
	}

	public function find(Project $project, int $id): ?ProformaInvoice
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'id' => $id]);
	}

	public function findByShoptet(Project $project, string $shoptetCode): ?ProformaInvoice
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'shoptetCode' => $shoptetCode]);
	}
}
