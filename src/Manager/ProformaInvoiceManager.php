<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\ProformaInvoiceRepository;

class ProformaInvoiceManager
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	public function getRepository(): ProformaInvoiceRepository
	{
		/** @var ProformaInvoiceRepository $repository */
		$repository = $this->entityManager->getRepository(ProformaInvoice::class);
		return $repository;
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
