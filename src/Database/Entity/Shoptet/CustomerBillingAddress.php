<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity()]
#[ORM\Table(name: 'sf_customer_billing_address')]
#[ORM\HasLifecycleCallbacks]
class CustomerBillingAddress extends CustomerAddress
{
	use Attributes\TId;

	#[ORM\OneToOne(targetEntity: Customer::class, cascade: ['remove'])]
	protected Customer $customer;

	public function setCustomer(Customer $customer): void
	{
		$this->customer = $customer;
	}
}
