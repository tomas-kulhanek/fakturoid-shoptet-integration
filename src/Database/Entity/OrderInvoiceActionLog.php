<?php

namespace App\Database\Entity;

use App\Database\Entity\Shoptet\Order;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class OrderInvoiceActionLog extends ActionLog
{
	#[ORM\ManyToOne(targetEntity: Order::class)]
	#[ORM\JoinColumn(name: 'order_id', nullable: true, onDelete: 'CASCADE')]
	protected Order $order;

	public function getActionLogType(): string
	{
		return 'order';
	}

	public function getDocument(): Order
	{
		return $this->order;
	}

	public function setDocument(Order $document): void
	{
		$this->order = $document;
	}
}
