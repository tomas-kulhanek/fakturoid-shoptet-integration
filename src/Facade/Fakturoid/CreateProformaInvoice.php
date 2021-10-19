<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidProformaInvoice;
use App\Database\Entity\Shoptet\DocumentItem;
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

	public function markAsPaid(ProformaInvoice $proformaInvoice, \DateTimeImmutable $payAt): void
	{
		if (!$proformaInvoice->getInvoice() instanceof \App\Database\Entity\Shoptet\Invoice) {
			return;
		}
		$this->accountingInvoice->markAsPaid($proformaInvoice, $payAt);
		$proformaInvoice->setPaid(true);
		$proformaInvoiceData = $this->accountingInvoice->getInvoiceData($proformaInvoice->getAccountingId(), $proformaInvoice->getProject()->getSettings());
		$invoice = $proformaInvoice->getInvoice();

		$accountingResponse = $this->accountingInvoice->getInvoiceData($proformaInvoiceData->getBody()->related_id, $proformaInvoice->getProject()->getSettings())
			->getBody();
		$invoice->setVarSymbol((int) $accountingResponse->variable_symbol);
		$invoice->setCode($accountingResponse->number);
		$invoice->setIsValid(true);

		if ($accountingResponse->taxable_fulfillment_due) {
			$date = \DateTimeImmutable::createFromFormat('Y-m-d', $accountingResponse->taxable_fulfillment_due);
			if ($date instanceof \DateTimeImmutable) {
				$invoice->setTaxDate($date);
			}
		}
		if ($accountingResponse->due_on) {
			$date = \DateTimeImmutable::createFromFormat('Y-m-d', $accountingResponse->due_on);
			if ($date instanceof \DateTimeImmutable) {
				$invoice->setDueDate($date);
			}
		}
		bdump($accountingResponse);
		$entities = [$invoice];
		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(function (DocumentItem $item) use ($line): bool {
				return $item->getName() === $line->name
					&& $item->getAmount() === (float) $line->quantity
					&& $item->getUnitWithoutVat() === (float) $line->unit_price;
			});
			if (!$items->isEmpty()) {
				/** @var DocumentItem $item */
				$item = $items->first();
				$item->setAccountingId($line->id);
				$entities[] = $item;
			}
		}
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

		$entities = [$invoice];
		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(function (DocumentItem $item) use ($line): bool {
				return $item->getName() === $line->name
					&& $item->getAmount() === (float) $line->quantity
					&& $item->getUnitWithoutVat() === (float) $line->unit_price;
			});
			if (!$items->isEmpty()) {
				/** @var DocumentItem $item */
				$item = $items->first();
				$item->setAccountingId($line->id);
				$entities[] = $item;
			}
		}
		if (
			$invoice->getBillingAddress()->getCompany() !== $invoice->getOrder()->getCustomer()->getBillingAddress()->getCompany()
			|| $invoice->getBillingAddress()->getCountryCode() !== $invoice->getOrder()->getCustomer()->getBillingAddress()->getCountryCode()
			|| $invoice->getBillingAddress()->getStreet() !== $invoice->getOrder()->getCustomer()->getBillingAddress()->getStreet()
			|| $invoice->getBillingAddress()->getCity() !== $invoice->getOrder()->getCustomer()->getBillingAddress()->getCity()
			|| $invoice->getBillingAddress()->getFullName() !== $invoice->getOrder()->getCustomer()->getBillingAddress()->getFullName()
			|| $invoice->getVatId() !== $invoice->getOrder()->getCustomer()->getVatId()
			|| $invoice->getCompanyId() !== $invoice->getOrder()->getCustomer()->getCompanyId()
		) {
			$invoiceBillingData = [];
			$invoiceBillingData['client_name'] = $invoice->getBillingAddress()->getFullName();
			if (($invoice->getCompanyId() !== null && $invoice->getCompanyId() !== '') || ($invoice->getVatId() !== null && $invoice->getVatId() !== '')) {
				$invoiceBillingData['client_name'] = $invoice->getBillingAddress()->getCompany();
			}
			$invoiceBillingData['client_street'] = $invoice->getBillingAddress()->getStreet();
			$invoiceBillingData['client_city'] = $invoice->getBillingAddress()->getCity();
			$invoiceBillingData['client_zip'] = $invoice->getBillingAddress()->getZip();
			$invoiceBillingData['client_country'] = $invoice->getBillingAddress()->getCountryCode();
			$invoiceBillingData['client_registration_no'] = $invoice->getCompanyId();
			$invoiceBillingData['client_vat_no'] = $invoice->getVatId();
			if (count(array_filter($invoiceBillingData)) > 0 && strlen((string) $invoiceBillingData['client_name']) > 0) {
				$this->accountingInvoice->update($invoice);
			}
		}
		$this->entityManager->flush($entities);
	}


	public function update(ProformaInvoice $invoice): void
	{
		if ($invoice->getOrder()->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getOrder()->getCustomer());
		}
		if ($invoice->getAccountingId() === null) {
			throw new \RuntimeException();
		}

		$accountingResponse = $this->accountingInvoice->update($invoice);
		//$invoice->setCode($accountingResponse->id);
		$invoice->setVarSymbol((int) $accountingResponse->variable_symbol);
		$invoice->setCode($accountingResponse->number);
		$invoice->setIsValid(true);

		if ($accountingResponse->due_on) {
			$date = \DateTimeImmutable::createFromFormat('Y-m-d', $accountingResponse->due_on);
			if ($date instanceof \DateTimeImmutable) {
				$invoice->setDueDate($date);
			}
		}
		bdump($accountingResponse);
		$entities = [$invoice];
		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(function (DocumentItem $item) use ($line): bool {
				return $item->getName() === $line->name
					&& $item->getAmount() === (float) $line->quantity
					&& $item->getUnitWithoutVat() === (float) $line->unit_price
					&& ($item->getAccountingId() === null || $item->getAccountingId() === $line->id);
			});
			if (!$items->isEmpty()) {
				/** @var DocumentItem $item */
				$item = $items->first();
				$item->setAccountingId($line->id);
				$entities[] = $item;
			}
		}
		$invoice->setAccountingPublicHtmlUrl($accountingResponse->public_html_url);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);

		$this->entityManager->flush($entities);
	}
}
