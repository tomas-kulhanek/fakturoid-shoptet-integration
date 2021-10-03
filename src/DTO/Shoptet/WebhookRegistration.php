<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use App\DTO\Shoptet\Request\Webhook;
use Symfony\Component\Validator\Constraints as Assert;

class WebhookRegistration
{
	#[Assert\NotBlank]
	#[Assert\Choice(choices: Webhook::ALL_TYPES)]
	public string $event;

	#[Assert\NotBlank]
	#[Assert\Url]
	public string $url;
}
