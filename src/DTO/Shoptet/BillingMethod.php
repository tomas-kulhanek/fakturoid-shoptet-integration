<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints as Assert;

class BillingMethod
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'integer')]
	public ?int $id = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $name = null;
}
