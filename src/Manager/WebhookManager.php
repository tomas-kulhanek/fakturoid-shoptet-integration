<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Application;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\Shoptet\ReceivedWebhook;
use App\Database\Entity\Shoptet\RegisteredWebhook;
use App\Database\EntityManager;
use App\DTO\Shoptet\Request\Webhook;
use App\DTO\Shoptet\WebhookRegistration;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\MessageBus\MessageBusDispatcher;
use GuzzleHttp\Exception\ClientException;
use Nette\Application\LinkGenerator;
use Psr\Log\LoggerInterface;

class WebhookManager
{
	public function __construct(
		private LinkGenerator        $urlGenerator,
		private EntityManager        $entityManager,
		private ClientInterface      $client,
		private MessageBusDispatcher $busDispatcher,
		private LoggerInterface      $logger
	)
	{
	}

	public function receive(Webhook $shoptetWebhook, Project $project): void
	{
		$webhook = $this->entityManager->getRepository(ReceivedWebhook::class)
			->findOneBy([
				'eshopId' => $project->getEshopId(),
				'event' => $shoptetWebhook->event,
				'eventInstance' => $shoptetWebhook->eventInstance,
			]);
		if (!$webhook instanceof ReceivedWebhook) {
			$webhook = new ReceivedWebhook(
				project: $project,
				eshopId: $shoptetWebhook->eshopId,
				event: $shoptetWebhook->event,
				eventInstance: $shoptetWebhook->eventInstance,
				eventCreated: $shoptetWebhook->eventCreated
			);
			$project->addReceivedWebhook($webhook);
			$this->entityManager->persist($webhook);
			$this->entityManager->persist($project);
		} else {
			$webhook->setLastReceived($shoptetWebhook->eventCreated);
		}
		$this->entityManager->flush();

		switch ($webhook->getEvent()) {
			case Webhook::TYPE_PROFORMA_INVOICE_CREATE:
			case Webhook::TYPE_PROFORMA_INVOICE_UPDATE:
			case Webhook::TYPE_PROFORMA_INVOICE_DELETE:
				if (!$project->getSettings()->isShoptetSynchronizeProformaInvoices()) {
					$this->logger->info('Skipping proforma invoice webhook.', [
						'eshopId' => $project->getEshopId(),
						'eventType' => $webhook->getEvent(),
						'eventCode' => $webhook->getEventInstance(),
					]);
					return;
				}
				break;
			case Webhook::TYPE_INVOICE_CREATE:
			case Webhook::TYPE_INVOICE_UPDATE:
			case Webhook::TYPE_INVOICE_DELETE:
				if (!$project->getSettings()->isShoptetSynchronizeInvoices()) {
					$this->logger->info('Skipping invoice webhook.', [
						'eshopId' => $project->getEshopId(),
						'eventType' => $webhook->getEvent(),
						'eventCode' => $webhook->getEventInstance(),
					]);
					return;
				}
				break;
			case Webhook::TYPE_CREDIT_NOTE_CREATE:
			case Webhook::TYPE_CREDIT_NOTE_UPDATE:
			case Webhook::TYPE_CREDIT_NOTE_DELETE:
				if (!$project->getSettings()->isShoptetSynchronizeCreditNotes()) {
					$this->logger->info('Skipping credit note webhook.', [
						'eshopId' => $project->getEshopId(),
						'eventType' => $webhook->getEvent(),
						'eventCode' => $webhook->getEventInstance(),
					]);
					return;
				}
				break;
		}
		$this->busDispatcher->dispatch($webhook);
	}

	public function unregisterHooks(Project $project): void
	{
		/** @var RegisteredWebhook $webhook */
		foreach ($project->getRegisteredWebhooks() as $webhook) {
			try {
				$this->client->unregisterWebHooks($webhook->getId(), $project);
				$this->entityManager->remove($webhook);
			} catch (ClientException $exception) {
				bdump($exception);
			}
		}
		$this->entityManager->flush();
	}

	public function registerOrderHooks(WebhookRegistrationRequest $webhooks): WebhookRegistrationRequest
	{
		foreach ([
					 Webhook::TYPE_ORDER_CREATE,
					 Webhook::TYPE_ORDER_UPDATE,
					 Webhook::TYPE_ORDER_DELETE,
				 ] as $webhookEventType) {
			$webhookRequest = new WebhookRegistration();
			$webhookRequest->url = $this->urlGenerator->link(Application::DESTINATION_WEBHOOK);
			$webhookRequest->event = $webhookEventType;
			$webhooks->data[] = $webhookRequest;
		}
		return $webhooks;
	}

	public function registerInvoiceHooks(WebhookRegistrationRequest $webhooks): WebhookRegistrationRequest
	{
		foreach ([
					 Webhook::TYPE_INVOICE_CREATE,
					 Webhook::TYPE_INVOICE_DELETE,
					 Webhook::TYPE_INVOICE_UPDATE,
				 ] as $webhookEventType) {
			$webhookRequest = new WebhookRegistration();
			$webhookRequest->url = $this->urlGenerator->link(Application::DESTINATION_WEBHOOK);
			$webhookRequest->event = $webhookEventType;
			$webhooks->data[] = $webhookRequest;
		}
		return $webhooks;
	}

	public function registerProformaInvoiceHooks(WebhookRegistrationRequest $webhooks): WebhookRegistrationRequest
	{
		foreach ([
					 Webhook::TYPE_PROFORMA_INVOICE_CREATE,
					 Webhook::TYPE_PROFORMA_INVOICE_DELETE,
					 Webhook::TYPE_PROFORMA_INVOICE_UPDATE,
				 ] as $webhookEventType) {
			$webhookRequest = new WebhookRegistration();
			$webhookRequest->url = $this->urlGenerator->link(Application::DESTINATION_WEBHOOK);
			$webhookRequest->event = $webhookEventType;
			$webhooks->data[] = $webhookRequest;
		}
		return $webhooks;
	}

	public function registerCreditNoteHooks(WebhookRegistrationRequest $webhooks): WebhookRegistrationRequest
	{
		foreach ([
					 Webhook::TYPE_CREDIT_NOTE_CREATE,
					 Webhook::TYPE_CREDIT_NOTE_DELETE,
					 Webhook::TYPE_CREDIT_NOTE_UPDATE,
				 ] as $webhookEventType) {
			$webhookRequest = new WebhookRegistration();
			$webhookRequest->url = $this->urlGenerator->link(Application::DESTINATION_WEBHOOK);
			$webhookRequest->event = $webhookEventType;
			$webhooks->data[] = $webhookRequest;
		}
		return $webhooks;
	}

	public function registerMandatoryHooks(WebhookRegistrationRequest $webhooks): WebhookRegistrationRequest
	{
		foreach ([
					 Webhook::TYPE_CUSTOMER_CREATE,
					 Webhook::TYPE_CUSTOMER_IMPORT,
					 Webhook::TYPE_ESHOP_MANDATORY_FIELDS,
				 ] as $webhookEventType) {
			$webhookRequest = new WebhookRegistration();
			$webhookRequest->url = $this->urlGenerator->link(Application::DESTINATION_WEBHOOK);
			$webhookRequest->event = $webhookEventType;
			$webhooks->data[] = $webhookRequest;
		}

		return $webhooks;
	}

	public function registerHooks(WebhookRegistrationRequest $webhooks, Project $project): void
	{
		$registeredWebhooks = $this->client->registerWebHooks($webhooks, $project);
		if ($registeredWebhooks->data !== null) {
			foreach ($registeredWebhooks->data->webhooks as $webhook) {
				$webhookEntity = new RegisteredWebhook(
					$webhook->id,
					$webhook->event,
					$webhook->url,
					$webhook->created,
					$project
				);
				$this->entityManager->persist($webhookEntity);
			}
		}
	}
}
