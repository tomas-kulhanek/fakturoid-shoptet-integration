<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Database\Entity\ProjectSetting;
use App\Database\EntityManager;
use App\DTO\Shoptet\Request\Webhook;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Manager\InvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\AccountingBusDispatcher;
use App\MessageBus\Message\Invoice;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadInvoiceMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProjectManager          $projectManager,
		private InvoiceManager          $invoiceManager,
		private EntityManager           $entityManager,
		private Fakturoid\Invoice       $fakturoidInvoice,
		private AccountingBusDispatcher $accountingBusDispatcher
	) {
	}

	public function __invoke(Invoice $invoice): void
	{
		dump($invoice::class);
		dump($this::class);
		$project = $this->projectManager->getByEshopId($invoice->getEshopId());
		switch ($invoice->getEventType()) {
			case Webhook::TYPE_INVOICE_CREATE:
			case Webhook::TYPE_INVOICE_UPDATE:
				$invoiceEntity = $this->invoiceManager->synchronizeFromShoptet($project, $invoice->getEventInstance());
				if ($invoiceEntity === null) {
					throw new \Exception();
				}
				if ($project->getSettings()->getAutomatization() !== ProjectSetting::AUTOMATIZATION_AUTO) {
					break;
				}

				try {
					$this->accountingBusDispatcher->dispatch($invoiceEntity);
				} catch (EmptyLines|FakturoidException) {
					//silent
				}
				break;
			case Webhook::TYPE_INVOICE_DELETE:
				$invoiceEntity = $this->invoiceManager->findByShoptet($project, $invoice->getEventInstance());
				if ($invoiceEntity instanceof \App\Database\Entity\Shoptet\Invoice) {
					$invoiceEntity->setDeletedAt(new \DateTimeImmutable());

					if ($invoiceEntity->getProject()->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
						if ($invoiceEntity->getAccountingId() !== NULL) {
							$this->fakturoidInvoice->cancel($invoiceEntity);
						}
					}
					foreach ($invoiceEntity->getItems() as $item) {
						$this->entityManager->remove($item);
					}
					$this->entityManager->remove($invoiceEntity);
					$this->entityManager->flush();
				}
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
