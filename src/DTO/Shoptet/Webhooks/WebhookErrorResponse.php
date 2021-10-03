<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Webhooks;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class WebhookErrorResponse
{
	#[NotBlank]
	public string $errorCode;
	public string $message;
	public string $instance;
}
