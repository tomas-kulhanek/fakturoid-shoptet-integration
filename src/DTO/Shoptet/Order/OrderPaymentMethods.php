<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use App\DTO\Shoptet\PaymentMethod;
use Symfony\Component\Validator\Constraints as Assert;

class OrderPaymentMethods
{
	#[Assert\NotBlank()]
	#[Assert\Type(type: PaymentMethod::class)]
	public PaymentMethod $paymentMethod;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'int')]
	public int $itemId;
}
