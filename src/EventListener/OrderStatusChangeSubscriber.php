<?php

declare(strict_types=1);


namespace App\EventListener;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\EntityManager;
use App\Event\OrderStatusChangeEvent;
use App\Facade\Fakturoid\CreateProformaInvoice;
use App\Facade\Fakturoid\Invoice;
use App\Facade\InvoiceCreateFacade;
use App\Facade\ProformaInvoiceCreateFacade;
use App\Log\ActionLog;
use App\Security\SecurityUser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderStatusChangeSubscriber implements EventSubscriberInterface
{
	public function __construct(
		private SecurityUser $user,
		private ClientInterface $client,
		private InvoiceCreateFacade $createFromOrderFacade,
		private ProformaInvoiceCreateFacade $ProformaInvoiceCreateFacade,
		private EntityManager $entityManager,
		private ActionLog $actionLog,
		protected CreateProformaInvoice $createProformaInvoice,
		private Invoice $invoiceFakturoid
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
			$this->actionLog->log($event->getOrder()->getProject(), ActionLog::UPDATE_ORDER, $event->getOrder()->getId());
		}
		if ($event->getNewStatus()->isCreateInvoice() && $event->getOrder()->getInvoices()->isEmpty()) {
			$items = [];
			foreach ($event->getOrder()->getItems() as $item) {
				$items[] = $item->getId();
			}
			if (!$event->getOrder()->getProformaInvoices()->isEmpty()) {
				/** @var ProformaInvoice $proforma */
				$proforma = $event->getOrder()->getProformaInvoices()->first();
				$invoice = $this->createFromOrderFacade->createFromProforma($proforma);
				if ($proforma->getAccountingId() !== null) {
					$this->createProformaInvoice->markAsPaid($proforma, new \DateTimeImmutable());
					$this->invoiceFakturoid->refresh($invoice);
				}
			} else {
				$invoice = $this->createFromOrderFacade->createFromOrder($event->getOrder(), $items);
				$this->entityManager->flush($invoice);
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
