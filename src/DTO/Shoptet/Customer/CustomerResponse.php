<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Customer;

use Symfony\Component\Validator\Constraints as Assert;

class CustomerResponse
{
	#[Assert\NotBlank()]
	#[Assert\Type(type: Customer::class)]
	public Customer $customer;
}
