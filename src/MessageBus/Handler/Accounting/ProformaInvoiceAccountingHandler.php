<?php

namespace App\MessageBus\Handler\Accounting;

use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Manager\ProformaInvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Accounting\ProformaInvoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProformaInvoiceAccountingHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProformaInvoiceManager                $invoiceManager,
		private ProjectManager                        $projectManager,
		private \App\Facade\Fakturoid\ProformaInvoice $accountingInvoice,
		private EntityManagerInterface                $entityManager
	) {
	}

	public function __invoke(ProformaInvoice $document): void
	{
		dump($document::class);
		dump($this::class);
		$project = $this->projectManager->getByEshopId($document->getEshopId());
		$invoice = $this->invoiceManager->find($project, $document->getDocumentId());
		try {
			if ($invoice->getAccountingId() === null) {
				$this->accountingInvoice->create($invoice);
			} else {
				$this->accountingInvoice->update($invoice);
			}

			if ($project->getSettings()->isAccountingSendMailInvoice() && ($invoice->getAccountingSentAt() === null || $project->getSettings()->isAccountingSendRepeatedlyMailInvoice())) {
				$this->accountingInvoice->sendMail($invoice);
			}
			//if ($invoice->isPaid() && !$invoice->isAccountingPaid()) {
			//	$this->accountingInvoice->markAsPaid($invoice, $invoice->getChangeTime() ?? $invoice->getCreationTime());
			//}
		} catch (FakturoidException $exception) {
			if ($exception->getCode() >= 500 && $exception->getCode() <= 599) {
				throw new UnrecoverableMessageHandlingException(
					'Chyba ve Fakturoidích obvodech',
					$exception->getCode(),
					$exception
				);
			}

			$this->entityManager->flush();

			if ($exception->getCode() >= 400 && $exception->getCode() <= 499) {
				$message = 'Chyba v zasílaných datech';
				if ($invoice->getAccountingId() === null && $exception->getCode() === 403) {
					$message = 'Ve Fakturoidím učtu není zadaný bankovní účet';
				}
				if ($invoice->getAccountingId() !== null && $exception->getCode() === 403) {
					$message = 'Uzamknutou fakturu nelze upravovat';
				}
				throw new UnrecoverableMessageHandlingException(
					$message,
					$exception->getCode(),
					$exception
				);
			}
		} catch (EmptyLines) {
			//silent
		}
	}
}
