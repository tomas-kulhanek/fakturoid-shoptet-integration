<?php

namespace App\MessageBus\Handler\Accounting;

use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Manager\InvoiceManager;
use App\Manager\ProformaInvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Accounting\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class InvoiceAccountingHandler implements MessageHandlerInterface
{
	public function __construct(
		private InvoiceManager            $invoiceManager,
		private ProformaInvoiceManager    $proformaInvoiceManager,
		private ProjectManager            $projectManager,
		private Fakturoid\Invoice         $accountingInvoice,
		private Fakturoid\ProformaInvoice $proformaInvoice,
		private EntityManagerInterface    $entityManager
	) {
	}

	public function __invoke(Invoice $document): void
	{
		dump($document::class);
		dump($this::class);
		$project = $this->projectManager->getByEshopId($document->getEshopId());
		try {
			$invoice = $this->invoiceManager->find($project, $document->getDocumentId());
		} catch (NoResultException) {
			throw new UnrecoverableMessageHandlingException();
		}
		try {
			$forcedUpdate = false;
			$proforma = $invoice->getProformaInvoice();
			if ($proforma instanceof ProformaInvoice && $proforma->getAccountingId() !== null && !$proforma->isAccountingPaid()) {
				$proforma = $this->proformaInvoiceManager->find($project, $invoice->getProformaInvoice()->getId());
				if (!$proforma->getInvoice() instanceof \App\Database\Entity\Shoptet\Invoice) {
					$proforma->setInvoice($invoice);
					$this->entityManager->persist($proforma);
				}
				if (!$this->proformaInvoice->markAsPaid($proforma, $invoice->getChangeTime() ?? $invoice->getCreationTime())) {
					throw new \Exception('Proforma cannot be mark as paid. But why?');
				}
				$this->entityManager->refresh($invoice);
				$this->entityManager->flush();
				$invoice = $this->invoiceManager->find($project, $document->getDocumentId());
				$forcedUpdate = true;
			}
			if ($invoice->getAccountingId() === null) {
				$this->accountingInvoice->create($invoice);
			} else {
				$this->accountingInvoice->update($invoice, true, $forcedUpdate);
			}
			if ($project->getSettings()->isAccountingSendMailInvoice() && ($invoice->getAccountingSentAt() === null || $project->getSettings()->isAccountingSendRepeatedlyMailInvoice())) {
				$this->accountingInvoice->sendMail($invoice);
			}
			try {
				if ($project->getSettings()->isAccountingMarkInvoiceAsPaid() && $invoice->isPaid() && !$invoice->isAccountingPaid()) {
					$this->accountingInvoice->markAsPaid($invoice, $invoice->getChangeTime() ?? $invoice->getCreationTime());
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
