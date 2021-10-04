<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidInvoice;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\EntityManager;

class CreateInvoice
{
	public function __construct(
		private FakturoidInvoice $fakturoidInvoice,
		private EntityManager $entityManager
	) {
	}


	public function create(Invoice $invoice): void
	{
		if ($invoice->getFakturoidId() !== null) {
			throw new \RuntimeException();
		}
		//todo eet!!
		$fakturoidResponse = $this->fakturoidInvoice->createNew($invoice);
		//$invoice->setCode($fakturoidResponse->id);
		$invoice->setVarSymbol((int) $fakturoidResponse->variable_symbol);
		$invoice->setCode($fakturoidResponse->number);
		$invoice->setIsValid(true);


		//$invoice->setFakturoidAcceptedAt($fakturoidResponse->accepted_at);
		//$invoice->setFakturoidCancelledAt($fakturoidResponse->);
		//$invoice->setFakturoidPaidAt($fakturoidResponse->paid_at);
		//$invoice->setFakturoidReminderSentAt($fakturoidResponse->reminder_sent_at);
		//$invoice->setFakturoidWebinvoiceSeenAt($fakturoidResponse->webinvoice_seen_at);
		$invoice->setFakturoidId($fakturoidResponse->id);
		$invoice->setFakturoidIssuedAt(new \DateTimeImmutable($fakturoidResponse->issued_on));
		$invoice->setFakturoidNumber($fakturoidResponse->number);
		if ($fakturoidResponse->sent_at) {
			$invoice->setFakturoidSentAt(new \DateTimeImmutable($fakturoidResponse->sent_at));
		}
		$invoice->setFakturoidSubjectId($fakturoidResponse->subject_id);
		//$fakturoidResponse->subject_id;
		//$fakturoidResponse->issued_on;
		//$fakturoidResponse->taxable_fulfillment_due;
		//$fakturoidResponse->due_on;
		//payment_method //todo to ani nenastavuji!!!

		$this->entityManager->flush($invoice);
	}
}
