<?php

declare(strict_types=1);


namespace App\Modules\Api\Fakturoid;

use App\Api\Client;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Order;
use App\Manager\InvoiceManager;
use App\Modules\Base\UnsecuredPresenter;
use App\Utils\Validator\Fakturoid\InitiatorValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Application\Responses\VoidResponse;
use Nette\Http\IResponse;
use Nette\Http\Response;
use Nette\Utils\Json;
use Nette\Utils\Strings;
use Tracy\Debugger;
use Tracy\ILogger;

class FakturoidPresenter extends UnsecuredPresenter
{
	public function __construct(
		private InvoiceManager         $invoiceManager,
		private EntityManagerInterface $entityManager,
		private Client                 $apiClient,
		private InitiatorValidatorInterface     $initiatorValidator
	)
	{
		parent::__construct();
	}

	public function actionWebhook(string $code): void
	{
		if (!$this->initiatorValidator->validateIpAddress($this->getHttpRequest())) {
			Debugger::log(sprintf('Divny request z %s na Fakturoid webhook!', $this->getHttpRequest()->getRemoteAddress()), ILogger::CRITICAL);
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		if (!$this->getRequest()->isMethod('POST')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		$webhook = Json::decode($this->getHttpRequest()->getRawBody());
		if (!Strings::startsWith($webhook->event_name, 'invoice_paid') && $webhook->event_name !== 'invoice_payment_removed') {
			$this->sendPayload();
		}
		$invoice = $this->invoiceManager->getByAccountingId($webhook->invoice_id);
		$invoice->getProject()->getSettings()->setAccountingLastHookUsedAt(new \DateTimeImmutable());
		$this->entityManager->flush();

		if (!$invoice instanceof Invoice) {
			$this->sendPayload();
		}
		if ($invoice->getProject()->getSettings()->getAccountingCode() !== $code) {
			$response = new VoidResponse();
			$response->setCode(Response::S401_UNAUTHORIZED);
			Debugger::log('Code is not valid!', ILogger::CRITICAL);
			$this->sendResponse($response);
		}

		if ($webhook->status !== 'paid' || !$webhook->paid_at) {
			$invoice->setAccountingPaidAt(NULL);
			$this->entityManager->flush();
			if ($invoice->getOrder() instanceof Order && $invoice->getOrder()->isPaid() && $invoice->getOrder()->getStatus()->getShoptetId() !== -4) {
				$this->apiClient->updateOrderStatus($invoice->getProject(), $invoice->getOrderCode(), NULL, FALSE);
			}
			$this->sendPayload();
		}

		$invoice->setAccountingPaidAt(\DateTimeImmutable::createFromFormat('Y-m-d', $webhook->paid_at));
		$this->entityManager->flush();
		if (!$invoice->getOrder() instanceof Order || $invoice->getOrder()->isPaid() || $invoice->getOrder()->getStatus()->getShoptetId() === -4) {
			$this->sendPayload();
		}

		$orderStatus = $invoice->getProject()->getOrderStatuses()->filter(fn(OrderStatus $status) => $status->isSetAfterPaidIsReceived())->first();
		if ($orderStatus instanceof OrderStatus) {
			$this->apiClient->updateOrderStatus($invoice->getProject(), $invoice->getOrderCode(), $orderStatus, TRUE);
		} else {
			$this->apiClient->updateOrderStatus($invoice->getProject(), $invoice->getOrderCode(), NULL, TRUE);
		}
		$this->sendPayload();
	}
}
