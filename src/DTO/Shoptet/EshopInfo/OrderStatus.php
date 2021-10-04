<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use Symfony\Component\Validator\Constraints as Assert;

class OrderStatus
{
	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $id;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $order;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'string')]
	public string $name;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public ?bool $markAsPaid = false;
}
