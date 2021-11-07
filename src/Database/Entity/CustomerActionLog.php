<?php

namespace App\Database\Entity;

use App\Database\Entity\Shoptet\Customer;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class CustomerActionLog extends ActionLog
{
	#[ORM\ManyToOne(targetEntity: Customer::class)]
	#[ORM\JoinColumn(name: 'customer_id', nullable: true, onDelete: 'CASCADE')]
	protected Customer $invoice;

	public function getActionLogType(): string
	{
		return 'invoice';
	}

	public function getDocument(): Customer
	{
		return $this->invoice;
	}

	public function setDocument(Customer $invoice): void
	{
		$this->invoice = $invoice;
	}
}
