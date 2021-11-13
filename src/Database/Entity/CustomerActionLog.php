<?php

namespace App\Database\Entity;

use App\Database\Entity\Shoptet\Customer;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class CustomerActionLog extends ActionLog
{
	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', nullable: true, onDelete: 'CASCADE')]
	protected Customer $customer;

	public function getActionLogType(): string
	{
		return 'customer';
	}

	public function getDocument(): Customer
	{
		return $this->customer;
	}

	public function setDocument(Customer $invoice): void
	{
		$this->customer = $invoice;
	}
}
