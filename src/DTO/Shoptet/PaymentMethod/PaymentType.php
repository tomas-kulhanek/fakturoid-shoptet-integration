<?php declare(strict_types=1);


namespace App\DTO\Shoptet\PaymentMethod;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentType
{

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $id;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $code;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $name;
}
