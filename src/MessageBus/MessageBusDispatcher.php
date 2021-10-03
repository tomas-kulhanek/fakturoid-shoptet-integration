<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\Database\Entity\Shoptet\ReceivedWebhook;
use App\DTO\Shoptet\Request\Webhook;
use App\MessageBus\Handler\DownloadCreditNoteMessageHandler;
use App\MessageBus\Handler\DownloadInvoiceMessageHandler;
use App\MessageBus\Handler\DownloadOrderMessageHandler;
use App\MessageBus\Handler\DownloadProformaInvoiceMessageHandler;
use App\MessageBus\Message\CreditNote;
use App\MessageBus\Message\Invoice;
use App\MessageBus\Message\Order;
use App\MessageBus\Message\ProformaInvoice;

class MessageBusDispatcher
{
	public function __construct(
		private DownloadCreditNoteMessageHandler $creditNoteMessageHandler,
		private DownloadInvoiceMessageHandler $invoiceMessageHandler,
		private DownloadOrderMessageHandler $orderMessageHandler,
		private DownloadProformaInvoiceMessageHandler $proformaInvoiceMessageHandler
	) {
	}

	public function dispatch(ReceivedWebhook $receivedWebhook): void
	{
		switch ($receivedWebhook->getEvent()) {
			case Webhook::TYPE_ORDER_CREATE:
			case Webhook::TYPE_ORDER_UPDATE:
			case Webhook::TYPE_ORDER_DELETE:
				$message = new Order($receivedWebhook);
				$this->orderMessageHandler->__invoke($message);
				break;
			case Webhook::TYPE_PROFORMA_INVOICE_CREATE:
			case Webhook::TYPE_PROFORMA_INVOICE_UPDATE:
			case Webhook::TYPE_PROFORMA_INVOICE_DELETE:
				$message = new ProformaInvoice($receivedWebhook);
				$this->proformaInvoiceMessageHandler->__invoke($message);
				break;
			case Webhook::TYPE_INVOICE_CREATE:
			case Webhook::TYPE_INVOICE_UPDATE:
			case Webhook::TYPE_INVOICE_DELETE:
				$message = new Invoice($receivedWebhook);
				$this->invoiceMessageHandler->__invoke($message);
				break;
			case Webhook::TYPE_CREDIT_NOTE_CREATE:
			case Webhook::TYPE_CREDIT_NOTE_UPDATE:
			case Webhook::TYPE_CREDIT_NOTE_DELETE:
				$message = new CreditNote($receivedWebhook);
				$this->creditNoteMessageHandler->__invoke($message);
				break;
			default:
				throw new \Exception('Unsupported event');
		}
		//$this->messageBus->dispatch($message, [new DelayStamp(5000)]);
	}
}
