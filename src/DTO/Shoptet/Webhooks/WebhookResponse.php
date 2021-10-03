<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Webhooks;

use App\DTO\Shoptet\Request\Webhook;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class WebhookResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'integer')]
	public int $id;
	#[Assert\NotBlank]
	#[Assert\Choice(choices: Webhook::ALL_TYPES)]
	public string $event;
	#[Assert\NotBlank]
	#[Assert\Url]
	public string $url;
	#[Assert\NotBlank]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public DateTimeImmutable $created;
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?DateTimeImmutable $updated = null;
}
