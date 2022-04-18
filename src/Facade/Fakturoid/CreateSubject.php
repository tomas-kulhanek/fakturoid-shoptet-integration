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
				if ($subject->registration_no === null) {
					continue;
				}
				if (Strings::padLeft($customer->getCompanyId(), 15, '0') === Strings::padLeft($subject->registration_no, 15, '0')) {
					$customer->setAccountingId($subject->id);
					$customer->setAccountingMapped(true);
					$this->entityManager->flush();

					if ($subject->type === 'supplier') {
						$this->accountingSubject->update($customer, $document, 'both');
					}
					return;
				}
			}
		}
		if ($customer->getEmail() !== null) {
			$subjects = $this->accountingSubject->findIdByQuery($customer->getEmail(), $customer->getProject());
			foreach ($subjects as $subject) {
				if ($subject->email === null) {
					continue;
				}
				if ($customer->getEmail() === $subject->email) {
					$customer->setAccountingId($subject->id);
					$customer->setAccountingMapped(true);
					$this->entityManager->flush();

					if ($subject->type === 'supplier') {
						$this->accountingSubject->update($customer, $document, 'both');
					}
					return;
				}
			}
		}

		$accountingResponse = $this->accountingSubject->createNew($customer, $document);
		$customer->setAccountingId($accountingResponse->id);
		$this->entityManager->flush();
	}

	public function update(Customer $customer, Document $document): void
	{
		if ($customer->getAccountingId() === null) {
			throw new \RuntimeException();
		}
		if (!$customer->isAccountingForUpdate()) {
			return;
		}

		$this->accountingSubject->update($customer, $document);
		$customer->setAccountingUpdatedAt(new \DateTimeImmutable());
		$customer->setAccountingForUpdate(false);
		$this->entityManager->flush();
	}
}
