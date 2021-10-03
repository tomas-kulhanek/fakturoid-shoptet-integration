<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use Symfony\Component\Validator\Constraints as Assert;

class OrderDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: OrderResponse::class)]
	public OrderResponse $data;
}
