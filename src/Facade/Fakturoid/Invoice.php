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
		private CreateSubject    $accountingSubject,
		private EntityManager    $entityManager,
		private SubjectDiff      $subjectDiff
	) {
	}

	public function markAsPaid(Shoptet\Invoice $invoice, \DateTimeImmutable $paidAt): void
	{
		if ($invoice->getAccountingId() === null) {
			$this->create($invoice);
		}
		$this->accountingInvoice->markAsPaid($invoice, $paidAt);
		$invoice->setAccountingPaidAt($paidAt);
		$invoice->setAccountingPaid(true);

		$this->entityManager->flush();
	}
	public function cancel(Shoptet\Invoice $invoice): void
	{
		if ($invoice->getAccountingId() === null) {
			return;
		}
		if ($invoice->isDeleted()) {
			$this->accountingInvoice->cancel($invoice);
		}
		$invoice->setProformaInvoice(null);
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
		//todo odchytit exception a zareagovat
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
		$invoice->setAccountingUpdatedAt(new \DateTimeImmutable());
		$invoice->setAccountingPublicHtmlUrl($accountingResponse->public_html_url);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);
		if ($this->subjectDiff->isDifferent($invoice)) {
			$this->accountingInvoice->update($invoice);
		}
		if ($flush) {
			$this->entityManager->flush($entities);
		}
	}

	public function update(Shoptet\Invoice $invoice, bool $flush = true): void
	{
		if (!$invoice->getProject()->getSettings()->isAccountingUpdate()) {
			return;
		}
		if ($invoice->getItems()->isEmpty()) {
			throw new EmptyLines();
		}
		if ($invoice->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getCustomer());
		}
		if ($invoice->getAccountingId() === null) {
			throw new \RuntimeException();
		}
		if ($invoice->getAccountingUpdatedAt() instanceof \DateTimeImmutable && $invoice->getChangeTime() <= $invoice->getAccountingUpdatedAt()) {
			return;
		}

		$accountingResponse = $this->accountingInvoice->update($invoice);
		//todo odchytit exception a zareagovat
		//$invoice->setCode($accountingResponse->id);
		$invoice->setVarSymbol((int) $accountingResponse->variable_symbol);
		$invoice->setCode($accountingResponse->number);
		$invoice->setIsValid(true);
		$invoice->setAccountingUpdatedAt(new \DateTimeImmutable());

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
