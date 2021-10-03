<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\DTO\Shoptet\Request\Webhook;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Invoice;
use App\Savers\InvoiceSaver;

class DownloadInvoiceMessageHandler
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private InvoiceSaver $saver
	) {
	}

	public function __invoke(Invoice $invoice): void
	{
		$project = $this->projectManager->getByEshopId($invoice->getEshopId());
		switch ($invoice->getEventType()) {
			case Webhook::TYPE_INVOICE_CREATE:
			case Webhook::TYPE_INVOICE_UPDATE:
				$invoiceData = $this->client->findInvoice(
					$invoice->getEventInstance(),
					$project
				);
				$this->saver->save($project, $invoiceData);
				break;
			case Webhook::TYPE_INVOICE_DELETE:
				//todo delete
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
