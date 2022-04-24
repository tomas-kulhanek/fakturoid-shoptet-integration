<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidInvoice;
use App\Database\Entity\Shoptet;
use App\Database\EntityManager;
use App\Exception\Accounting\EmptyLines;
use App\Mapping\BillingMethodMapper;

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
		if ($invoice->isAccountingPaid()) {
			return;
		}
		$markAsPaid = $invoice->getProject()->getSettings()->isAccountingMarkInvoiceAsPaid() && $invoice->isPaid();
		if (!$markAsPaid) {
			return;
		}
		if ($invoice->getOrder() === null || !$invoice->getOrder()->isPaid()) {
			return;
		}

		if ($invoice->getAccountingId() === null) {
			$this->create($invoice);
		}
		$this->accountingInvoice->markAsPaid($invoice, $paidAt);
		$invoice->setAccountingPaidAt($paidAt);
		$invoice->setAccountingPaid(true);

		$this->entityManager->flush();
	}

	public function sendMail(Shoptet\Invoice $invoice): void
	{
		$projectSettings = $invoice->getProject()->getSettings();
		if (!$projectSettings->isAccountingSendMailInvoice() || ($invoice->getAccountingSentAt() !== null && !$projectSettings->isAccountingSendRepeatedlyMailInvoice())) {
			return;
		}
		$sendByMail = true;
		if ($invoice->getBillingMethod() === BillingMethodMapper::BILLING_METHOD_COD) {
			$sendByMail = $invoice->getOrder() !== null && $invoice->getOrder()->isPaid();
		}

		if (!$sendByMail) {
			return;
		}
		$this->accountingInvoice->sendMail($invoice);
		$invoice->setAccountingSentAt(new \DateTimeImmutable());
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

	public function create(Shoptet\Invoice $invoice, bool $flush = true): void
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
		$accountingResponse = $this->accountingInvoice->createNew($invoice);
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
		//if ($invoice->getEet() !== NULL && !empty($accountingResponse->eet_records)) {
		//	$invoice->getEet()->setAccountingId($accountingResponse->eet_records[0]->id);
		//}

		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(fn (Shoptet\DocumentItem $item): bool => $this->accountingInvoice->getLineName($item) === $line->name
				&& $item->getAmount() === (float)$line->quantity
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
		if ($this->subjectDiff->isDifferent($invoice)) {
			$this->accountingInvoice->update($invoice);
		}
		if ($flush) {
			$this->entityManager->flush();
		}
	}

	public function update(Shoptet\Invoice $invoice, bool $flush = true, bool $forcedUpdate = false): void
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

		$accountingResponse = $this->accountingInvoice->update($invoice);
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

		if ($invoice->getEet() !== NULL && !empty($accountingResponse->eet_records)) {
			$invoice->getEet()->setAccountingId($accountingResponse->eet_records[0]->id);
		}
		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(fn (Shoptet\DocumentItem $item): bool => $this->accountingInvoice->getLineName($item) === $line->name
				&& $item->getAmount() === (float)$line->quantity
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
