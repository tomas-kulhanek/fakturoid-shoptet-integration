<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Webhooks;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class WebhookDataResponse
{
	/** @var WebhookResponse[] */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, WebhookResponse>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Webhooks\WebhookResponse>')]
	public array $webhooks = [];
}
