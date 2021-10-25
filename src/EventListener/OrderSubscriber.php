<?php

declare(strict_types=1);


namespace App\EventListener;

use App\Api\ClientInterface;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\EntityManager;
use App\Event\NewOrderEvent;
use App\Event\OrderStatusChangeEvent;
use App\Facade\Fakturoid\CreateProformaInvoice;
use App\Facade\Fakturoid\Invoice;
use App\Facade\InvoiceCreateFacade;
use App\Facade\ProformaInvoiceCreateFacade;
use App\Log\ActionLog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderSubscriber implements EventSubscriberInterface
{
	public function __construct(
		private ClientInterface             $client,
		private InvoiceCreateFacade         $createFromOrderFacade,
		private ProformaInvoiceCreateFacade $proformaInvoiceCreateFacade,
		private EntityManager               $entityManager,
		private ActionLog                   $actionLog,
		protected CreateProformaInvoice     $createProformaInvoice,
		private Invoice                     $invoiceFakturoid
	) {
	}

	public static function getSubscribedEvents(): array
	{
		return [
			NewOrderEvent::class => 'newOrder',
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

		if ($event->isGui()) {
			$this->client->updateOrderStatus($event->getOrder()->getProject(), $event->getOrder()->getShoptetCode(), $event->getNewStatus());
			$this->actionLog->log($event->getOrder()->getProject(), ActionLog::UPDATE_ORDER, $event->getOrder()->getId());
		}

		$this->processRelatedDocuments($event->getOrder(), $event->getNewStatus());
	}

	protected function processRelatedDocuments(Order $order, OrderStatus $orderStatus): void
	{
		$items = [];
		foreach ($order->getItems() as $item) {
			$items[] = $item->getId();
		}
		$canCreateInvoice = $orderStatus->isCreateInvoice() && $order->getInvoices()->isEmpty();
		$this->entityManager->refresh($order);

		if ($canCreateInvoice && $order->getProformaInvoices()->isEmpty()) {
			$this->createFromOrderFacade->createFromOrder($order, $items);
		}

		if ($canCreateInvoice && !$order->getProformaInvoices()->isEmpty()) {
			/** @var ProformaInvoice $proforma */
			$proforma = $order->getProformaInvoices()->first();
			if (!$proforma->getInvoice() instanceof Invoice) {
				$invoice = $this->createFromOrderFacade->createFromProforma($proforma);
				if ($proforma->getAccountingId() !== null) {
					$this->createProformaInvoice->markAsPaid($proforma, new \DateTimeImmutable());
					$this->invoiceFakturoid->refresh($invoice);
				}
			}
		}

		if ($orderStatus->isCreateProforma() && $order->getProformaInvoices()->isEmpty()) {
			$this->proformaInvoiceCreateFacade->createFromOrder($order, $items);
		}
	}

	public function newOrder(NewOrderEvent $event): void
	{
		if (!in_array($event->getOrder()->getProject()->getSettings()->getAutomatization(), [ProjectSetting::AUTOMATIZATION_SEMI_AUTO, ProjectSetting::AUTOMATIZATION_AUTO], true)) {
			return;
		}
		$this->processRelatedDocuments($event->getOrder(), $event->getOrder()->getStatus());
	}
}
