<?php

declare(strict_types=1);

namespace App\Database\Entity\Attributes;

use Doctrine\ORM\Mapping as ORM;

trait TCreatedAt
{
	/**
	 * @ORM\Column(type="datetime_immutable", nullable=FALSE)
	 */
	protected \DateTimeImmutable $createdAt;

	public function getCreatedAt(): \DateTimeImmutable
	{
		return $this->createdAt;
	}

	/**
	 * @ORM\PrePersist
	 * @internal
	 */
	public function setCreatedAt(): void
	{
		$this->createdAt = new \DateTimeImmutable();
	}
}
