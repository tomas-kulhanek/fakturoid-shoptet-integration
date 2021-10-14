<?php

declare(strict_types=1);


namespace App\MessageBus\Message\Synchronization;

abstract class SynchronizationMessage
{
	public function __construct(
		private int $eshopId,
		private \DateTimeImmutable $dateTimeImmutable
	) {
	}

	public function getEshopId(): int
	{
		return $this->eshopId;
	}

	public function getDateTimeImmutable(): \DateTimeImmutable
	{
		return $this->dateTimeImmutable;
	}
}
