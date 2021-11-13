<?php

namespace App\MessageBus\Handler\Accounting;

use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Manager\InvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Accounting\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class InvoiceAccountingHandler implements MessageHandlerInterface
{
	public function __construct(
		private InvoiceManager            $invoiceManager,
		private ProjectManager            $projectManager,
		private Fakturoid\Invoice         $accountingInvoice,
		private Fakturoid\ProformaInvoice $proformaInvoice,
		private EntityManagerInterface    $entityManager
	) {
	}

	public function __invoke(Invoice $document): void
	{
		dump(get_class($document));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($document->getEshopId());
		$invoice = $this->invoiceManager->find($project, $document->getDocumentId());
		try {
			if ($invoice->getProformaInvoice() instanceof ProformaInvoice && $invoice->getProformaInvoice()->getAccountingId() !== null && !$invoice->getProformaInvoice()->isAccountingPaid()) {
				$this->proformaInvoice->markAsPaid($invoice->getProformaInvoice(), $invoice->getProformaInvoice()->getChangeTime() ?? $invoice->getProformaInvoice()->getCreationTime());
			}
			if ($invoice->getAccountingId() === null) {
				$this->accountingInvoice->create($invoice);
			} else {
				$this->accountingInvoice->update($invoice);
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
			if ($exception->getCode() >= 400 && $exception->getCode() <= 499) {
				$this->projectManager->disableAutomatization($invoice->getProject(), $exception->getCode());
			}
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
