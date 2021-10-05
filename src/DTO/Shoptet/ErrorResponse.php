<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class ErrorResponse
{
	#[NotBlank]
	public string $errorCode;
	public string $message;
	public string $instance;
}
