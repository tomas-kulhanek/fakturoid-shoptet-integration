<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints as Assert;

class WebhookRegistrationRequest
{
	/** @var WebhookRegistration[] */
	#[Assert\NotBlank]
	public array $data = [];
}
