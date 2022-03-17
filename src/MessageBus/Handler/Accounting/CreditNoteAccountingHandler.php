<?php

namespace App\MessageBus\Handler\Accounting;

use App\Database\Entity\Shoptet\Invoice;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Manager\CreditNoteManager;
use App\Manager\InvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Accounting\CreditNote;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreditNoteAccountingHandler implements MessageHandlerInterface
{
	public function __construct(
		private CreditNoteManager      $creditNoteManager,
		private ProjectManager         $projectManager,
		private Fakturoid\CreditNote      $accountingInvoice,
		private EntityManagerInterface $entityManager,
		private InvoiceManager $invoiceManager
	) {
	}

	public function __invoke(CreditNote $document): void
	{
		dump($document::class);
		dump($this::class);
		$project = $this->projectManager->getByEshopId($document->getEshopId());
		try {
			$creditNote = $this->creditNoteManager->find($project, $document->getDocumentId());
		} catch (NoResultException) {
			throw new UnrecoverableMessageHandlingException();
		}
		try {
			$forcedUpdate = false;
			//todo co faktura?


			$invoice = $creditNote->getInvoice();
			if ($invoice instanceof Invoice && $invoice->getAccountingId() !== null && !$invoice->isAccountingPaid()) {
				$invoice = $this->invoiceManager->find($project, $creditNote->getInvoice()->getId());

				$this->entityManager->refresh($invoice);
				$this->entityManager->flush();
				$creditNote = $this->creditNoteManager->find($project, $document->getDocumentId());
				$forcedUpdate = true;
			}

			if ($creditNote->getAccountingId() === null) {
				$this->accountingInvoice->create($creditNote);
			} else {
				$this->accountingInvoice->update($creditNote, true, $forcedUpdate);
			}
			try {
				if ($creditNote->isPaid() && !$creditNote->isAccountingPaid()) {
					$this->accountingInvoice->markAsPaid($creditNote, $creditNote->getChangeTime() ?? $creditNote->getCreationTime());
				}
			} catch (\Exception) {
			}
		} catch (FakturoidException $exception) {
			if ($exception->getCode() >= 500 && $exception->getCode() <= 599) {
				throw new UnrecoverableMessageHandlingException(
					'Chyba ve Fakturoidích obvodech',
					$exception->getCode(),
					$exception
				);
			}
			//if ($exception->getCode() >= 400 && $exception->getCode() <= 499) {
			//	$this->projectManager->disableAutomatization($invoice->getProject(), $exception->getCode());
			//}
			$this->entityManager->flush();

			if ($exception->getCode() >= 400 && $exception->getCode() <= 499) {
				throw new UnrecoverableMessageHandlingException(
					'Chyba v zasílaných datech',
					$exception->getCode(),
					$exception
				);
			}
		} catch (EmptyLines) {
			//silent
		}
	}
}
