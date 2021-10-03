<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use Symfony\Component\Validator\Constraints as Assert;

class OrderNoteAdditionalFields
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'int')]
	public int $index;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $label = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $text = null;
}
