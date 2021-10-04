<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

class EshopInfoErrorResponse
{
	#[NotBlank]
	public string $errorCode;
	public string $message;
	public string $instance;
}
