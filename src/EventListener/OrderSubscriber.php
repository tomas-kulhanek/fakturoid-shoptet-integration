<?php

declare(strict_types=1);


namespace App\EventListener;

use App\Api\ClientInterface;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\EntityManager;
use App\Event\OrderStatusChangeEvent;
use App\Facade\Fakturoid;
use App\Facade\InvoiceCreateFacade;
use App\Facade\ProformaInvoiceCreateFacade;
use App\Log\ActionLog;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Tracy\Debugger;
use Tracy\ILogger;

class OrderSubscriber implements EventSubscriberInterface
{
	public function __construct(
		private ClientInterface             $client,
		private InvoiceCreateFacade         $createFromOrderFacade,
		private ProformaInvoiceCreateFacade $proformaInvoiceCreateFacade,
		private EntityManager               $entityManager,
		private ActionLog                   $actionLog,
		protected Fakturoid\ProformaInvoice $createProformaInvoice,
		private Fakturoid\Invoice           $invoiceFakturoid
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
		if ($event->getOrder()->getProject()->getSettings()->getAutomatization() !== ProjectSetting::AUTOMATIZATION_AUTO) {
			return;
		}

		if ($event->isGui()) {
			$this->client->updateOrderStatus($event->getOrder()->getProject(), $event->getOrder()->getShoptetCode(), $event->getNewStatus(), $event->getOrder()->isPaid());
			$this->actionLog->logOrder($event->getOrder()->getProject(), ActionLog::UPDATE_ORDER, $event->getOrder());
		}
		foreach ($event->getOrder()->getInvoices() as $invoice) {
			$this->markAsPaid($invoice);
			$this->sendByMail($invoice);
		}

		//$this->processRelatedDocuments($event->getOrder(), $event->getNewStatus());
	}



	private function markAsPaid(\App\Database\Entity\Shoptet\Invoice $invoice): void
	{
		try {
			$this->invoiceFakturoid->markAsPaid($invoice, $invoice->getChangeTime() ?? $invoice->getCreationTime());
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::EXCEPTION);
		}
	}


	private function sendByMail(\App\Database\Entity\Shoptet\Invoice $invoice): void
	{
		try {
			$this->invoiceFakturoid->sendMail($invoice);
		} catch (\Exception $exception) {
			Debugger::log($exception, ILogger::EXCEPTION);
		}
	}

	protected function processRelatedDocuments(Order $order, OrderStatus $orderStatus): void
	{
		$canCreateInvoice = $orderStatus->isCreateInvoice() && $order->getInvoices()->isEmpty();
		$this->entityManager->refresh($order);

		if ($canCreateInvoice && $order->getProformaInvoices()->isEmpty()) {
			$this->createFromOrderFacade->createFromOrder($order);
		}

		if ($canCreateInvoice && !$order->getProformaInvoices()->isEmpty()) {
			/** @var ProformaInvoice $proforma */
			$proforma = $order->getProformaInvoices()->first();
			if (!$proforma->getInvoice() instanceof \App\Database\Entity\Shoptet\Invoice) {
				$invoice = $this->createFromOrderFacade->createFromProforma($proforma);
				if ($proforma->getAccountingId() !== null && !$proforma->isAccountingPaid()) {
					$this->createProformaInvoice->markAsPaid($proforma, new \DateTimeImmutable());
					$this->invoiceFakturoid->refresh($invoice);
				}
			}
		}

		if ($orderStatus->isCreateProforma() && $order->getProformaInvoices()->isEmpty()) {
			$this->proformaInvoiceCreateFacade->createFromOrder($order);
		}
	}
}
