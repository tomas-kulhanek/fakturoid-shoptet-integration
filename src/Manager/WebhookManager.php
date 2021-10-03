<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\Shoptet\ReceivedWebhook;
use App\Database\Entity\Shoptet\RegisteredWebhook;
use App\Database\EntityManager;
use App\DTO\Shoptet\Request\Webhook;
use App\DTO\Shoptet\WebhookRegistration;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\MessageBus\MessageBusDispatcher;
use Nette\Application\LinkGenerator;

class WebhookManager
{
	public function __construct(
		private LinkGenerator $urlGenerator,
		private EntityManager $entityManager,
		private ClientInterface $client,
		private MessageBusDispatcher $busDispatcher
	) {
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
		$this->busDispatcher->dispatch($webhook);
	}

	public function registerHooks(Project $project): void
	{
		$webhooks = new WebhookRegistrationRequest();
		foreach ([
					 Webhook::TYPE_ORDER_CREATE,
					 Webhook::TYPE_ORDER_UPDATE,
					 Webhook::TYPE_ORDER_DELETE,
					 Webhook::TYPE_CREDIT_NOTE_CREATE,
					 Webhook::TYPE_CREDIT_NOTE_DELETE,
					 Webhook::TYPE_CREDIT_NOTE_UPDATE,
					 Webhook::TYPE_CUSTOMER_IMPORT,
					 Webhook::TYPE_DELIVERY_NOTE_CREATE,
					 Webhook::TYPE_DELIVERY_NOTE_DELETE,
					 Webhook::TYPE_DELIVERY_NOTE_UPDATE,
					 Webhook::TYPE_ESHOP_MANDATORY_FIELDS,
					 Webhook::TYPE_INVOICE_CREATE,
					 Webhook::TYPE_INVOICE_DELETE,
					 Webhook::TYPE_INVOICE_UPDATE,
					 Webhook::TYPE_PROFORMA_INVOICE_CREATE,
					 Webhook::TYPE_PROFORMA_INVOICE_DELETE,
					 Webhook::TYPE_PROFORMA_INVOICE_UPDATE,

				 ] as $webhookEventType) {
			$webhookRequest = new WebhookRegistration();
			$webhookRequest->url = $this->urlGenerator->link('Api:Shoptet:webhook');
			$webhookRequest->event = $webhookEventType;
			$webhooks->data[] = $webhookRequest;
		}
		$registeredWebhooks = $this->client->registerWebHooks($webhooks, $project);
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
		$this->entityManager->flush();
	}
}
