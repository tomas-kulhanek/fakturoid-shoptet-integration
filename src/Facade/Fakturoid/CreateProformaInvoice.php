<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\EntityManager;

class CreateProformaInvoice
{
	public function __construct(
		private FakturoidProformaInvoice $accountingInvoice,
		private CreateSubject $accountingSubject,
		private EntityManager $entityManager
	) {
	}

	public function markAsPaid(ProformaInvoice $invoice, \DateTimeImmutable $payAt): void
	{
		$this->accountingInvoice->markAsPaid($invoice, $payAt);
		$invoice->setPaid(true);
		$this->entityManager->flush($invoice);
	}

	public function create(ProformaInvoice $invoice): void
	{
		if ($invoice->getOrder()->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getOrder()->getCustomer());
		}
		//if ($invoice->getAccountingId() !== null) {
		//	throw new \RuntimeException();
		//}
		//todo eet!!
		$accountingResponse = $this->accountingInvoice->createNew($invoice);
		//$invoice->setCode($accountingResponse->id);
		$invoice->setVarSymbol((int) $accountingResponse->variable_symbol);
		$invoice->setCode($accountingResponse->number);
		$invoice->setIsValid(true);

		$invoice->setAccountingPublicHtmlUrl($accountingResponse->public_html_url);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);

		$this->entityManager->flush($invoice);
	}
}
