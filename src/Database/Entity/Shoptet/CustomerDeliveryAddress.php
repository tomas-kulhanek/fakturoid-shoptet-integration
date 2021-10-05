<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity()]
#[ORM\Table(name: 'sf_customer_delivery_address')]
#[ORM\HasLifecycleCallbacks]
class CustomerDeliveryAddress extends CustomerAddress
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', nullable: false, onDelete: 'CASCADE')]
	protected Customer $customer;

	public function setCustomer(Customer $customer): void
	{
		$this->customer = $customer;
	}

	public function getControlHash(): string
	{
		return sha1(
			serialize([
				$this->company,
				$this->fullName,
				$this->street,
				$this->houseNumber,
				$this->city,
				$this->district,
				$this->additional,
				$this->zip,
				$this->countryCode,
				$this->regionName,
				$this->regionShortcut,
			])
		);
	}
}
