<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Webhooks;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class WebhookCreatedResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: WebhookDataResponse::class)]
	#[Serializer\Type(name: WebhookDataResponse::class)]
	public ?WebhookDataResponse $data = null;

	/** @var WebhookErrorResponse[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, WebhookErrorResponse>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Webhook\WebhookErrorResponse>')]
	public ?array $errors = null;

	public function hasErrors(): bool
	{
		return $this->errors !== null && count($this->errors) > 0;
	}
}
