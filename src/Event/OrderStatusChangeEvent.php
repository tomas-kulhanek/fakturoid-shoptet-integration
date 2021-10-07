<?php

declare(strict_types=1);


namespace App\Event;

use App\Database\Entity\OrderStatus;
use App\Database\Entity\Shoptet\Order;
use App\Security\Identity;

use Symfony\Contracts\EventDispatcher\Event;

class OrderStatusChangeEvent extends Event
{
	public function __construct(
		private Order       $order,
		private OrderStatus $oldStatus,
		private OrderStatus $newStatus,
		private bool        $gui = true
	)
	{
	}

	public function isGui(): bool
	{
		return $this->gui;
	}

	public function getOrder(): Order
	{
		return $this->order;
	}

	public function getOldStatus(): OrderStatus
	{
		return $this->oldStatus;
	}

	public function getNewStatus(): OrderStatus
	{
		return $this->newStatus;
	}
}
