<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\CustomerRepository;
use App\Savers\Shoptet\CustomerSaver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class CustomerManager
{
	public function __construct(
		private EntityManager            $entityManager,
		private EventDispatcherInterface $eventDispatcher,
		private ClientInterface          $shoptetClient,
		private CustomerSaver            $customerSaver
	) {
	}

	public function getRepository(): CustomerRepository
	{
		/** @var CustomerRepository $repository */
		$repository = $this->entityManager->getRepository(Customer::class);
		return $repository;
	}

	public function find(Project $project, int $id): ?Customer
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'id' => $id]);
	}

	public function findByGuid(Project $project, string $guid): ?Customer
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'guid' => $guid]);
	}

	public function synchronizeFromShoptet(Project $project, string $id): ?Customer
	{
		$customerData = $this->shoptetClient->findCustomer($id, $project);
		bdump($customerData);
		return $this->customerSaver->save($project, $customerData);
	}
}
