<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\Database\EntityManager;
use App\DTO\Shoptet\Request\Webhook;
use App\Manager\InvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Invoice;
use App\Savers\InvoiceSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadInvoiceMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private InvoiceManager $invoiceManager,
		private EntityManager $entityManager,
		private InvoiceSaver $saver
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
				if (!$invoiceData->hasErrors()) {
					$this->saver->save($project, $invoiceData->data->invoice);
				}
				break;
			case Webhook::TYPE_INVOICE_DELETE:
				$invoiceEntity = $this->invoiceManager->findByShoptet($project, $invoice->getEventInstance());
				$invoiceEntity->setDeletedAt(new \DateTimeImmutable());
				$this->entityManager->flush($invoiceEntity);
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
