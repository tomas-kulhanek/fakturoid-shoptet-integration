<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidCreditNote;
use App\Database\Entity\Shoptet;
use App\Database\EntityManager;
use App\Exception\Accounting\EmptyLines;

class CreditNote
{
	public function __construct(
		private FakturoidCreditNote $fakturoidCreditNote,
		private CreateSubject       $accountingSubject,
		private EntityManager       $entityManager
	) {
	}

	public function markAsPaid(Shoptet\CreditNote $creditNote, \DateTimeImmutable $paidAt): void
	{
		if ($creditNote->isAccountingPaid()) {
			return;
		}
		if ($creditNote->getAccountingId() === null) {
			$this->create($creditNote);
		}
		$this->fakturoidCreditNote->markAsPaid($creditNote, $paidAt);
		$creditNote->setAccountingPaidAt($paidAt);
		$creditNote->setAccountingPaid(true);

		$this->entityManager->flush();
	}

	public function cancel(Shoptet\CreditNote $invoice): void
	{
		if ($invoice->getAccountingId() === null) {
			return;
		}
		if ($invoice->isDeleted()) {
			$this->fakturoidCreditNote->cancel($invoice);
		}
	}

	public function refresh(Shoptet\CreditNote $invoice, bool $flush = true): void
	{
		$accountingResponse = $this->fakturoidCreditNote->getByGuid($invoice->getProject(), $invoice->getGuid());
		$invoice->setVarSymbol((string)$accountingResponse->variable_symbol);
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
		$invoice->setAccountingNumberLineId($accountingResponse->number_format_id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);
		if ($flush) {
			$this->entityManager->flush();
		}
	}

	public function create(Shoptet\CreditNote $invoice, bool $flush = true): void
	{
		if ($invoice->getItems()->isEmpty()) {
			throw new EmptyLines();
		}
		if ($invoice->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getCustomer(), $invoice);
		} else {
			$this->accountingSubject->update($invoice->getCustomer(), $invoice);
		}
		if ($invoice->getAccountingId() !== null) {
			throw new \RuntimeException();
		}
		//todo eet!!
		$accountingResponse = $this->fakturoidCreditNote->createNew($invoice);
		//todo odchytit exception a zareagovat
		//$invoice->setCode($accountingResponse->id);
		$invoice->setVarSymbol((string)$accountingResponse->variable_symbol);
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

		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(fn (Shoptet\DocumentItem $item): bool => $this->fakturoidCreditNote->getLineName($item) === $line->name
				&& ($this->fakturoidCreditNote->getLineAmount($item)) === (float)$line->quantity
				&& $item->getAccountingId() === null);
			if (!$items->isEmpty()) {
				/** @var Shoptet\DocumentItem $item */
				$item = $items->first();
				$item->setAccountingId($line->id);
			}
		}
		$invoice->setAccountingUpdatedAt($invoice->getChangeTime() ?? $invoice->getCreationTime());
		$invoice->setAccountingPublicHtmlUrl($accountingResponse->public_html_url);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingNumberLineId($accountingResponse->number_format_id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);
		//if ($this->subjectDiff->isDifferent($invoice)) {
		//	$this->fakturoidCreditNote->update($invoice);
		//}
		if ($flush) {
			$this->entityManager->flush();
		}
	}

	public function update(Shoptet\CreditNote $invoice, bool $flush = true, bool $forcedUpdate = false): void
	{
		if (!$forcedUpdate && !$invoice->getProject()->getSettings()->isAccountingUpdate()) {
			return;
		}
		if ($invoice->getItems()->isEmpty()) {
			throw new EmptyLines();
		}
		if ($invoice->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getCustomer(), $invoice);
		} else {
			$this->accountingSubject->update($invoice->getCustomer(), $invoice);
		}
		if ($invoice->getAccountingId() === null) {
			throw new \RuntimeException();
		}
		if ($invoice->getAccountingUpdatedAt() instanceof \DateTimeImmutable && $invoice->getChangeTime() <= $invoice->getAccountingUpdatedAt()) {
			return;
		}

		$accountingResponse = $this->fakturoidCreditNote->update($invoice);
		//todo odchytit exception a zareagovat
		//$invoice->setCode($accountingResponse->id);
		$invoice->setVarSymbol((string)$accountingResponse->variable_symbol);
		$invoice->setCode($accountingResponse->number);
		$invoice->setIsValid(true);
		$invoice->setAccountingUpdatedAt($invoice->getChangeTime() ?? $invoice->getCreationTime());

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

		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(fn (Shoptet\DocumentItem $item): bool => $this->fakturoidCreditNote->getLineName($item) === $line->name
				&& ($this->fakturoidCreditNote->getLineAmount($item)) === (float)$line->quantity
				&& ($item->getAccountingId() === null || $item->getAccountingId() === $line->id));
			if (!$items->isEmpty()) {
				/** @var Shoptet\DocumentItem $item */
				$item = $items->first();
				$item->setAccountingId($line->id);
			}
		}
		$invoice->setAccountingPublicHtmlUrl($accountingResponse->public_html_url);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingNumberLineId($accountingResponse->number_format_id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingNumber($accountingResponse->number);
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);
		if ($flush) {
			$this->entityManager->flush();
		}
	}
}
