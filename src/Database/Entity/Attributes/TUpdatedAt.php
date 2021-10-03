<?php

declare(strict_types=1);

namespace App\Database\Entity\Attributes;

use Doctrine\ORM\Mapping as ORM;

trait TUpdatedAt
{
	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?\DateTimeImmutable $updatedAt = null;

	public function getUpdatedAt(): ?\DateTimeImmutable
	{
		return $this->updatedAt;
	}

	/** @internal */
	#[ORM\PreUpdate]
	public function setUpdatedAt(): void
	{
		$this->updatedAt = new \DateTimeImmutable();
	}
}
