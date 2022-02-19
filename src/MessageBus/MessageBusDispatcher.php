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
		$message = match ($receivedWebhook->getEvent()) {
			Webhook::TYPE_ORDER_CREATE, Webhook::TYPE_ORDER_UPDATE, Webhook::TYPE_ORDER_DELETE => new Order(
				eshopId: $receivedWebhook->getEshopId(),
				eventType: $receivedWebhook->getEvent(),
				eventInstance: $receivedWebhook->getEventInstance(),
				webhookId: $receivedWebhook->getId()
			),
			Webhook::TYPE_PROFORMA_INVOICE_CREATE, Webhook::TYPE_PROFORMA_INVOICE_UPDATE, Webhook::TYPE_PROFORMA_INVOICE_DELETE => new ProformaInvoice(
				eshopId: $receivedWebhook->getEshopId(),
				eventType: $receivedWebhook->getEvent(),
				eventInstance: $receivedWebhook->getEventInstance(),
				webhookId: $receivedWebhook->getId()
			),
			Webhook::TYPE_INVOICE_CREATE, Webhook::TYPE_INVOICE_UPDATE, Webhook::TYPE_INVOICE_DELETE => new Invoice(
				eshopId: $receivedWebhook->getEshopId(),
				eventType: $receivedWebhook->getEvent(),
				eventInstance: $receivedWebhook->getEventInstance(),
				webhookId: $receivedWebhook->getId()
			),
			Webhook::TYPE_CREDIT_NOTE_CREATE, Webhook::TYPE_CREDIT_NOTE_UPDATE, Webhook::TYPE_CREDIT_NOTE_DELETE => new CreditNote(
				eshopId: $receivedWebhook->getEshopId(),
				eventType: $receivedWebhook->getEvent(),
				eventInstance: $receivedWebhook->getEventInstance(),
				webhookId: $receivedWebhook->getId()
			),
			Webhook::TYPE_CUSTOMER_CREATE, Webhook::TYPE_CUSTOMER_IMPORT => new Customer(
				eshopId: $receivedWebhook->getEshopId(),
				eventType: $receivedWebhook->getEvent(),
				eventInstance: $receivedWebhook->getEventInstance(),
				webhookId: $receivedWebhook->getId()
			),
			default => throw new \Exception('Unsupported event'),
		};
		$stamps = [new DelayStamp(5000)];
		if ($this->user->isLoggedIn()) {
			$stamps[] = new UserStamp($this->user->getId());
		}
		$this->messageBus->dispatch($message, $stamps);
	}
}
