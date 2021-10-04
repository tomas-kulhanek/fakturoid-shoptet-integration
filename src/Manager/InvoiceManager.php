<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\InvoiceRepository;

class InvoiceManager
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	public function getRepository(): InvoiceRepository
	{
		/** @var InvoiceRepository $repository */
		$repository = $this->entityManager->getRepository(Invoice::class);
		return $repository;
	}

	public function find(Project $project, int $id): Invoice
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'id' => $id]);
	}
}
