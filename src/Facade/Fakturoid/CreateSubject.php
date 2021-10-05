<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidSubject;
use App\Database\Entity\Shoptet\Customer;
use App\Database\EntityManager;

class CreateSubject
{
	public function __construct(
		private FakturoidSubject $fakturoidSubject,
		private EntityManager    $entityManager
	) {
	}


	public function create(Customer $customer): void
	{
		if ($customer->getFakturoidId() !== null) {
			throw new \RuntimeException();
		}
		$fakturoidResponse = $this->fakturoidSubject->createNew($customer);

		$customer->setFakturoidId($fakturoidResponse->id);
		//$customer->setFakturoidCreatedAt(fakt);

		$this->entityManager->flush($customer);
	}
}
