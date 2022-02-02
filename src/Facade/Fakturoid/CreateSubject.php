<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidSubject;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Document;
use App\Database\EntityManager;

class CreateSubject
{
	public function __construct(
		private FakturoidSubject $accountingSubject,
		private EntityManager    $entityManager
	) {
	}


	public function create(Customer $customer, Document $document): void
	{
		if ($customer->getAccountingId() !== null) {
			throw new \RuntimeException();
		}
		$accountingResponse = $this->accountingSubject->createNew($customer, $document);

		$customer->setAccountingId($accountingResponse->id);
		//$customer->setAccountingCreatedAt(fakt);

		$this->entityManager->flush();
	}
}
