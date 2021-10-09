<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\Database\Entity\Shoptet\ReceivedWebhook;
use App\DTO\Shoptet\Request\Webhook;
use App\MessageBus\Message\CreditNote;
use App\MessageBus\Message\Customer;
use App\MessageBus\Message\Invoice;
use App\MessageBus\Message\Order;
use App\MessageBus\Message\ProformaInvoice;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class SynchronizeMessageBusDispatcher
{
	public function __construct(
		private MessageBusInterface $messageBus
	) {
	}

	public function dispatch(ReceivedWebhook $receivedWebhook): void
	{
		switch ($receivedWebhook->getEvent()) {
			case Webhook::TYPE_ORDER_CREATE:
			case Webhook::TYPE_ORDER_UPDATE:
			case Webhook::TYPE_ORDER_DELETE:
				$message = new Order(
					eshopId: $receivedWebhook->getEshopId(),
					eventType: $receivedWebhook->getEvent(),
					eventInstance: $receivedWebhook->getEventInstance(),
					webhookId: $receivedWebhook->getId()
				);
				break;
			case Webhook::TYPE_PROFORMA_INVOICE_CREATE:
			case Webhook::TYPE_PROFORMA_INVOICE_UPDATE:
			case Webhook::TYPE_PROFORMA_INVOICE_DELETE:
				$message = new ProformaInvoice(
					eshopId: $receivedWebhook->getEshopId(),
					eventType: $receivedWebhook->getEvent(),
					eventInstance: $receivedWebhook->getEventInstance(),
					webhookId: $receivedWebhook->getId()
				);
				break;
			case Webhook::TYPE_INVOICE_CREATE:
			case Webhook::TYPE_INVOICE_UPDATE:
			case Webhook::TYPE_INVOICE_DELETE:
				$message = new Invoice(
					eshopId: $receivedWebhook->getEshopId(),
					eventType: $receivedWebhook->getEvent(),
					eventInstance: $receivedWebhook->getEventInstance(),
					webhookId: $receivedWebhook->getId()
				);
				break;
			case Webhook::TYPE_CREDIT_NOTE_CREATE:
			case Webhook::TYPE_CREDIT_NOTE_UPDATE:
			case Webhook::TYPE_CREDIT_NOTE_DELETE:
				$message = new CreditNote(
					eshopId: $receivedWebhook->getEshopId(),
					eventType: $receivedWebhook->getEvent(),
					eventInstance: $receivedWebhook->getEventInstance(),
					webhookId: $receivedWebhook->getId()
				);
				break;
			case Webhook::TYPE_CUSTOMER_CREATE:
			case Webhook::TYPE_CUSTOMER_IMPORT:
				$message = new Customer(
					eshopId: $receivedWebhook->getEshopId(),
					eventType: $receivedWebhook->getEvent(),
					eventInstance: $receivedWebhook->getEventInstance(),
					webhookId: $receivedWebhook->getId()
				);
				break;
			default:
				throw new \Exception('Unsupported event');
		}
		$this->messageBus->dispatch($message, [new DelayStamp(5000)]);
	}
}
