<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidInvoice;
use App\Database\Entity\Shoptet;
use App\Database\EntityManager;
use App\Exception\Accounting\EmptyLines;

class Invoice
{
	public function __construct(
		private FakturoidInvoice $accountingInvoice,
		private CreateSubject $accountingSubject,
		private EntityManager $entityManager
	) {
	}

	public function refresh(Shoptet\Invoice $invoice, bool $flush = true): void
	{
		$accountingResponse = $this->accountingInvoice->getByGuid($invoice->getProject(), $invoice->getGuid());
		$invoice->setVarSymbol((int) $accountingResponse->variable_symbol);
		$invoice->setCode($accountingResponse->number);
		$invoice->setIsValid(true);
		$invoice->setPaid($accountingResponse->paid_at !== null && $accountingResponse->paid_at !== '');

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

		$invoice->setAccountingPublicHtmlUrl($accountingResponse->public_html_url);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);
		if ($flush) {
			$this->entityManager->flush($invoice);
		}
	}

	public function create(Shoptet\Invoice $invoice, bool $flush = true): void
	{
		if ($invoice->getItems()->isEmpty()) {
			throw new EmptyLines();
		}
		if ($invoice->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getCustomer());
		}
		if ($invoice->getAccountingId() !== null) {
			throw new \RuntimeException();
		}
		//todo eet!!
		$accountingResponse = $this->accountingInvoice->createNew($invoice);
		//$invoice->setCode($accountingResponse->id);
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
			$items = $invoice->getItems()->filter(function (Shoptet\DocumentItem $item) use ($line): bool {
				return $item->getName() === $line->name
					&& $item->getAmount() === (float) $line->quantity
					&& $item->getUnitWithoutVat() === (float) $line->unit_price;
			});
			if (!$items->isEmpty()) {
				/** @var Shoptet\DocumentItem $item */
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
		if (
			$invoice->getBillingAddress()->getCompany() !== $invoice->getCustomer()->getBillingAddress()->getCompany()
			|| $invoice->getBillingAddress()->getCountryCode() !== $invoice->getCustomer()->getBillingAddress()->getCountryCode()
			|| $invoice->getBillingAddress()->getStreet() !== $invoice->getCustomer()->getBillingAddress()->getStreet()
			|| $invoice->getBillingAddress()->getCity() !== $invoice->getCustomer()->getBillingAddress()->getCity()
			|| $invoice->getBillingAddress()->getFullName() !== $invoice->getCustomer()->getBillingAddress()->getFullName()
			|| $invoice->getVatId() !== $invoice->getCustomer()->getVatId()
			|| $invoice->getCompanyId() !== $invoice->getCustomer()->getCompanyId()
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
		if ($flush) {
			$this->entityManager->flush($entities);
		}
	}

	public function update(Shoptet\Invoice $invoice, bool $flush = true): void
	{
		if ($invoice->getItems()->isEmpty()) {
			throw new EmptyLines();
		}
		if ($invoice->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getCustomer());
		}
		if ($invoice->getAccountingId() === null) {
			throw new \RuntimeException();
		}

		$accountingResponse = $this->accountingInvoice->update($invoice);
		//$invoice->setCode($accountingResponse->id);
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
			$items = $invoice->getItems()->filter(function (Shoptet\DocumentItem $item) use ($line): bool {
				return $item->getName() === $line->name
					&& $item->getAmount() === (float) $line->quantity
					&& $item->getUnitWithoutVat() === (float) $line->unit_price
					&& ($item->getAccountingId() === null || $item->getAccountingId() === $line->id);
			});
			if (!$items->isEmpty()) {
				/** @var Shoptet\DocumentItem $item */
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
		if ($flush) {
			$this->entityManager->flush($entities);
		}
	}
}
