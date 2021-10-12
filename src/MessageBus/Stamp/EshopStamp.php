<?php

declare(strict_types=1);


namespace App\MessageBus\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class EshopStamp implements StampInterface
{
	public function __construct(
		protected int $eshopId
	) {
	}

	public function getEshopId(): int
	{
		return $this->eshopId;
	}
}
