<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\SignatureKey;

use Symfony\Component\Validator\Constraints as Assert;

class SignatureKeyDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $signatureKey;
}
