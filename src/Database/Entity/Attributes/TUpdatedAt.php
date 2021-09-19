<?php

declare(strict_types=1);

namespace App\Database\Entity\Attributes;

use Doctrine\ORM\Mapping as ORM;

trait TUpdatedAt
{
	/**
	 * @ORM\Column(type="datetime_immutable", nullable=TRUE)
	 */
	protected ?\DateTimeImmutable $updatedAt = null;

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updatedAt;
	}

	/**
	 * @ORM\PreUpdate
	 * @internal
	 */
	public function setUpdatedAt(): void
	{
		$this->updatedAt = new \DateTimeImmutable();
	}
}
