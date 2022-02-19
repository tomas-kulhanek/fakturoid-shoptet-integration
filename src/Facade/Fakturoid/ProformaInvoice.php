<?php

declare(strict_types=1);


namespace App\Facade\Fakturoid;

use App\Connector\FakturoidProformaInvoice;
use App\Database\Entity\Shoptet;
use App\Database\EntityManager;
use App\Exception\Accounting\EmptyLines;

class ProformaInvoice
{
	public function __construct(
		private FakturoidProformaInvoice $accountingInvoice,
		private CreateSubject            $accountingSubject,
		private EntityManager            $entityManager,
		private SubjectDiff              $subjectDiff
	) {
	}

	public function cancel(Shoptet\ProformaInvoice $proformaInvoice): void
	{
		if ($proformaInvoice->getAccountingId() === null) {
			return;
		}
		if ($proformaInvoice->isDeleted()) {
			$this->accountingInvoice->cancel($proformaInvoice);
		}
	}

	public function markAsPaid(Shoptet\ProformaInvoice $proformaInvoice, \DateTimeImmutable $paidAt): bool
	{
		if (!$proformaInvoice->getInvoice() instanceof Shoptet\Invoice) {
			return false;
		}
		$this->accountingInvoice->markAsPaid($proformaInvoice, $paidAt);
		$proformaInvoice->setAccountingUpdatedAt(new \DateTimeImmutable());
		$proformaInvoice->setAccountingPaid(true);
		$proformaInvoice->setAccountingPaidAt($paidAt);
		$proformaInvoiceData = $this->accountingInvoice->getInvoiceData($proformaInvoice->getAccountingId(), $proformaInvoice->getProject()->getSettings());
		$invoice = $proformaInvoice->getInvoice();

		$accountingResponse = $this->accountingInvoice->getInvoiceData($proformaInvoiceData->getBody()->related_id, $proformaInvoice->getProject()->getSettings())
			->getBody();

		bdump($accountingResponse);

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
		$invoice->setAccountingPublicHtmlUrl($accountingResponse->public_html_url);
		$invoice->setAccountingId($accountingResponse->id);
		$invoice->setAccountingIssuedAt(new \DateTimeImmutable($accountingResponse->issued_on));
		$invoice->setAccountingPaidAt($paidAt);
		$invoice->setAccountingPaid(true);
		$invoice->setAccountingNumber($accountingResponse->number);
		//$invoice->setAccountingUpdatedAt(new \DateTimeImmutable());
		if ($accountingResponse->sent_at) {
			$invoice->setAccountingSentAt(new \DateTimeImmutable($accountingResponse->sent_at));
		}
		$invoice->setAccountingSubjectId($accountingResponse->subject_id);

		$this->entityManager->flush();
		return true;
	}

	public function create(Shoptet\ProformaInvoice $invoice, bool $flush = true): void
	{
		if ($invoice->getItems()->isEmpty()) {
			throw new EmptyLines();
		}
		if ($invoice->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getCustomer(), $invoice);
		}
		if ($invoice->getAccountingId() !== null) {
			throw new \RuntimeException();
		}

		$accountingResponse = $this->accountingInvoice->createNew($invoice);
		//todo odchytit exception a zareagovat
		//$invoice->setCode($accountingResponse->id);
		$invoice->setVarSymbol((string)$accountingResponse->variable_symbol);
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

		/** @var \stdClass $line */
		foreach ($accountingResponse->lines as $line) {
			$items = $invoice->getItems()->filter(fn (Shoptet\DocumentItem $item): bool => $this->accountingInvoice->getLineName($item) === $line->name
					&& $item->getAmount() === (float)$line->quantity);
			if (!$items->isEmpty()) {
				/** @var Shoptet\DocumentItem $item */
				$item = $items->first();
				$item->setAccountingId($line->id);
			}
		}
		if ($this->subjectDiff->isDifferent($invoice)) {
			$this->accountingInvoice->update($invoice);
		}
		if ($flush) {
			$this->entityManager->flush();
		}
	}

	public function update(Shoptet\ProformaInvoice $invoice, bool $flush = true): void
	{
		if (!$invoice->getProject()->getSettings()->isAccountingUpdate()) {
			return;
		}
		if ($invoice->getItems()->isEmpty()) {
			throw new EmptyLines();
		}
		if ($invoice->getCustomer()->getAccountingId() === null) {
			$this->accountingSubject->create($invoice->getCustomer(), $invoice);
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
		$invoice->setAccountingUpdatedAt(new \DateTimeImmutable());

		if ($accountingResponse->due_on) {
			$date = \DateTimeImmutable::createFromFormat('Y-m-d', $accountingResponse->due_on);
			if ($date instanceof \DateTimeImmutable) {
				$invoice->setDueDate($date);
			}
		}
		bdump($accountingResponse);

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
