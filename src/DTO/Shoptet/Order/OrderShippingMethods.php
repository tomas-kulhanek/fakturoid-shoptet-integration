<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use App\DTO\Shoptet\ShippingMethod;
use Symfony\Component\Validator\Constraints as Assert;

class OrderShippingMethods
{
	#[Assert\NotBlank()]
	#[Assert\Type(type: ShippingMethod::class)]
	public ShippingMethod $shipping;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'int')]
	public int $itemId;
}
