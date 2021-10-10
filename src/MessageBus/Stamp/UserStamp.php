<?php

declare(strict_types=1);


namespace App\MessageBus\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class UserStamp implements StampInterface
{
	public function __construct(
		protected int $userId
	) {
	}

	public function getUserId(): int
	{
		return $this->userId;
	}
}
