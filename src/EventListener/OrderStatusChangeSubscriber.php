<?php

declare(strict_types=1);


namespace App\EventListener;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\EntityManager;
use App\Event\OrderStatusChangeEvent;
use App\Facade\InvoiceCreateFacade;
use App\Facade\ProformaInvoiceCreateFacade;
use App\Security\SecurityUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderStatusChangeSubscriber implements EventSubscriberInterface
{
	public function __construct(
		private SecurityUser $user,
		private ClientInterface $client,
		private InvoiceCreateFacade $createFromOrderFacade,
		private ProformaInvoiceCreateFacade $ProformaInvoiceCreateFacade,
		private EntityManager $entityManager
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
		if (!in_array($event->getOrder()->getProject()->getSettings()->getAutomatization(), [ProjectSetting::AUTOMATIZATION_SEMI_AUTO, ProjectSetting::AUTOMATIZATION_AUTO], true)) {
			return;
		}
		bdump($this->client);
		if ($event->isGui()) {
			$this->client->updateOrderStatus($event->getOrder()->getProject(), $event->getOrder()->getShoptetCode(), $event->getNewStatus());
		}
		if ($event->getNewStatus()->isCreateInvoice() && $event->getOrder()->getInvoices()->isEmpty()) {
			$items = [];
			foreach ($event->getOrder()->getItems() as $item) {
				$items[] = $item->getId();
			}
			$invoice = $this->createFromOrderFacade->createFromOrder($event->getOrder(), $items);
			if (!$event->getOrder()->getProformaInvoices()->isEmpty()) {
				/** @var ProformaInvoice $proforma */
				$proforma = $event->getOrder()->getProformaInvoices()->first();
				$invoice->setProformaInvoice($proforma);
				$proforma->setInvoice($invoice);
				$this->entityManager->flush([$invoice, $proforma]);
			}
		}
		if ($event->getNewStatus()->isCreateProforma() && $event->getOrder()->getProformaInvoices()->isEmpty()) {
			$items = [];
			foreach ($event->getOrder()->getItems() as $item) {
				$items[] = $item->getId();
			}
			$this->ProformaInvoiceCreateFacade->createFromOrder($event->getOrder(), $items);
		}
		bdump($event);
		bdump($this->user);
		//todo aktualizovat do Shoptetu a taky aplikovat logiku pro accounting
	}
}
