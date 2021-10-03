<?php

declare(strict_types=1);


namespace App\MessageBus\Message;

use App\Database\Entity\Shoptet\ReceivedWebhook;
use App\DTO\Shoptet\Request\Webhook;
use Symfony\Component\Validator\Constraints\Choice;

abstract class Message
{
	private int $eshopId;

	#[Choice(choices: Webhook::ALL_TYPES)]
	private string $eventType;
	private ?string $eventInstance = null;
	private int $webhookId;

	public function __construct(
		ReceivedWebhook $receivedWebhook
	) {
		$this->eshopId = $receivedWebhook->getEshopId();
		$this->eventType = $receivedWebhook->getEvent();
		$this->eventInstance = $receivedWebhook->getEventInstance();
		$this->webhookId = (int) $receivedWebhook->getId();
	}

	public function getWebhookId(): int
	{
		return $this->webhookId;
	}

	public function getEshopId(): int
	{
		return $this->eshopId;
	}

	public function getEventType(): string
	{
		return $this->eventType;
	}

	public function getEventInstance(): ?string
	{
		return $this->eventInstance;
	}
}
