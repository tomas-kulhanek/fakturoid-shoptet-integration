<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidSubject;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Document;
use App\Database\EntityManager;
use Nette\Utils\Strings;

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
		if ($customer->getCompanyId() !== null) {
			$subjects = $this->accountingSubject->findIdByQuery($customer->getCompanyId(), $customer->getProject());
			foreach ($subjects as $subject) {
				if ($subject->type === 'supplier') {
					continue;
				}
				if (Strings::padLeft($customer->getCompanyId(), 15, '0') === Strings::padLeft($subject->registration_no, 15, '0')) {
					$customer->setAccountingId($subject->id);
					$customer->setAccountingMapped(true);
					$this->entityManager->flush();
					return;
				}
			}
		}

		$accountingResponse = $this->accountingSubject->createNew($customer, $document);
		$customer->setAccountingId($accountingResponse->id);
		$this->entityManager->flush();
	}
}
