<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints as Assert;

class ItemRecyclingFee
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $category = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $fee = null;
}
