<?php

namespace App\DTO\Shoptet\Webhooks;

use App\DTO\Shoptet\ErrorResponse;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class WebhookListResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: WebhookDataResponse::class)]
	#[Serializer\Type(name: WebhookDataResponse::class)]
	public ?WebhookDataResponse $data = null;

	/** @var ErrorResponse[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, ErrorResponse>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\ErrorResponse>')]
	public ?array $errors = null;

	public function hasErrors(): bool
	{
		return $this->errors !== null && count($this->errors) > 0;
	}
}
