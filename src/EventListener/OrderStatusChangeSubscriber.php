<?php

declare(strict_types=1);


namespace App\EventListener;

use App\Api\ClientInterface;
use App\Event\OrderStatusChangeEvent;
use App\Facade\InvoiceCreateFromOrderFacade;
use App\Security\SecurityUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderStatusChangeSubscriber implements EventSubscriberInterface
{
	public function __construct(
		private SecurityUser                 $user,
		private ClientInterface              $client,
		private InvoiceCreateFromOrderFacade $createFromOrderFacade
	) {
	}

	public static function getSubscribedEvents(): array
	{
		return [
			OrderStatusChangeEvent::class => 'statusChange',
		];
	}

	public function statusChange(OrderStatusChangeEvent $event): void
	{
		if ($event->getNewStatus()->getId() === $event->getOldStatus()->getId()) {
			return;
		}
		bdump($this->client);
		$this->client->updateOrderStatus($event->getOrder()->getProject(), $event->getOrder()->getShoptetCode(), $event->getNewStatus());
		if ($event->getNewStatus()->isCreateInvoice() && $event->getOrder()->getInvoices()->isEmpty()) {
			$this->createFromOrderFacade->create($event->getOrder());
		}
		bdump($event);
		bdump($this->user);
		//todo aktualizovat do Shoptetu a taky aplikovat logiku pro accounting
	}
}
