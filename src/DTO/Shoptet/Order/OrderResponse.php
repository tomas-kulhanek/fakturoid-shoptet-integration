<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use Symfony\Component\Validator\Constraints as Assert;

class OrderResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: Order::class)]
	public Order $order;
}
