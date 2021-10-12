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
use App\MessageBus\Stamp\EshopStamp;
use App\MessageBus\Stamp\UserStamp;
use App\Security\SecurityUser;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class MessageBusDispatcher
{
	public function __construct(
		private MessageBusInterface $messageBus,
		private SecurityUser        $user
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
		$stamps = [new DelayStamp(5000), new EshopStamp($receivedWebhook->getEshopId())];
		if ($this->user->isLoggedIn()) {
			$stamps[] = new UserStamp($this->user->getId());
		}
		$this->messageBus->dispatch($message, $stamps);
	}
}
