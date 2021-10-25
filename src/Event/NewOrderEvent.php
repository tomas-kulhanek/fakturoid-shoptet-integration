<?php

declare(strict_types=1);


namespace App\Event;

use App\Database\Entity\Shoptet\Order;
use Symfony\Contracts\EventDispatcher\Event;

class NewOrderEvent extends Event
{
	public function __construct(
		private Order $order
	) {
	}

	public function getOrder(): Order
	{
		return $this->order;
	}
}
