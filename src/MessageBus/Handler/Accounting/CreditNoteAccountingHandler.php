<?php

namespace App\MessageBus\Handler\Accounting;

use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Manager\CreditNoteManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Accounting\CreditNote;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreditNoteAccountingHandler implements MessageHandlerInterface
{
	public function __construct(
		private CreditNoteManager      $invoiceManager,
		private ProjectManager         $projectManager,
		private Fakturoid\CreditNote      $accountingInvoice,
		private EntityManagerInterface $entityManager
	) {
	}

	public function __invoke(CreditNote $document): void
	{
		dump(get_class($document));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($document->getEshopId());
		try {
			$invoice = $this->invoiceManager->find($project, $document->getDocumentId());
		} catch (NoResultException) {
			throw new UnrecoverableMessageHandlingException();
		}
		try {
			$forcedUpdate = false;
			//todo co faktura?
			if ($invoice->getAccountingId() === null) {
				$this->accountingInvoice->create($invoice);
			} else {
				$this->accountingInvoice->update($invoice, true, $forcedUpdate);
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
