<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\EntityManager;
use App\DTO\Shoptet\Invoice\InvoiceResponse;
use App\DTO\Shoptet\Request\Webhook;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Manager\InvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\AccountingBusDispatcher;
use App\MessageBus\Message\Invoice;
use App\Savers\InvoiceSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadInvoiceMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface           $client,
		private ProjectManager            $projectManager,
		private InvoiceManager            $invoiceManager,
		private EntityManager             $entityManager,
		private InvoiceSaver              $saver,
		private Fakturoid\Invoice         $fakturoidInvoice,
		private AccountingBusDispatcher   $accountingBusDispatcher
	) {
	}

	public function __invoke(Invoice $invoice): void
	{
		dump(get_class($invoice));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($invoice->getEshopId());
		switch ($invoice->getEventType()) {
			case Webhook::TYPE_INVOICE_CREATE:
			case Webhook::TYPE_INVOICE_UPDATE:
				$invoiceData = $this->client->findInvoice(
					$invoice->getEventInstance(),
					$project
				);

				if (!$invoiceData->hasErrors() && $invoiceData->data instanceof InvoiceResponse) {
					$invoice = $this->saver->save($project, $invoiceData->data->invoice);

					if ($invoice->getProject()->getSettings()->getAutomatization() !== ProjectSetting::AUTOMATIZATION_AUTO) {
						break;
					}
					try {
						$this->accountingBusDispatcher->dispatch($invoice);
					} catch (EmptyLines | FakturoidException) {
						//silent
					}
				}
				break;
			case Webhook::TYPE_INVOICE_DELETE:
				$invoiceEntity = $this->invoiceManager->findByShoptet($project, $invoice->getEventInstance());
				$invoiceEntity->setDeletedAt(new \DateTimeImmutable());

				if ($invoiceEntity->getProject()->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) { //todo asi bych taky hodil do redisu
					if ($invoiceEntity->getAccountingId() !== null) {
						$this->fakturoidInvoice->cancel($invoiceEntity);
					}
				}
				$this->entityManager->remove($invoiceEntity);
				$this->entityManager->flush();
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
