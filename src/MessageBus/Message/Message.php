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
	private ?int $webhookId = null;

	public function __construct(
		int $eshopId,
		string $eventType,
		?string $eventInstance,
		?int $webhookId
	) {
		$this->eshopId = $eshopId;
		$this->eventType = $eventType;
		$this->eventInstance = $eventInstance;
		$this->webhookId = $webhookId;
	}

	public function getWebhookId(): ?int
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
