<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidInvoice;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\EntityManager;

class CreateInvoice
{
	public function __construct(
		private FakturoidInvoice $accountingInvoice,
		private EntityManager $entityManager
	) {
	}


	public function create(Invoice $invoice): void
	{
		if ($invoice->getAccountingId() !== null) {
			throw new \RuntimeException();
		}
		//todo eet!!
		$accountingResponse = $this->accountingInvoice->createNew($invoice);
		//$invoice->setCode($accountingResponse->id);
		$invoice->setVarSymbol((int) $accountingResponse->variable_symbol);
		$invoice->setCode($accountingResponse->number);
		$invoice->setIsValid(true);


		//$invoice->setAccountingAcceptedAt($accountingResponse->accepted_at);
		//$invoice->setAccountingCancelledAt($accountingResponse->);
		//$invoice->setAccountingPaidAt($accountingResponse->paid_at);
		//$invoice->setAccountingReminderSentAt($accountingResponse->reminder_sent_at);
		//$invoice->setAccountingWebinvoiceSeenAt($accountingResponse->webinvoice_seen_at);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);
		//$accountingResponse->subject_id;
		//$accountingResponse->issued_on;
		//$accountingResponse->taxable_fulfillment_due;
		//$accountingResponse->due_on;
		//payment_method //todo to ani nenastavuji!!!

		$this->entityManager->flush($invoice);
	}
}
