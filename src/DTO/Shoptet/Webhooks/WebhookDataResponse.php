<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Webhooks;

use Symfony\Component\Validator\Constraints as Assert;

class WebhookDataResponse
{
	/** @var WebhookResponse[] */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, WebhookResponse>')]
	public array $webhooks = [];
}
