<?php

declare(strict_types=1);

namespace App\Database\Entity\Attributes;

use Doctrine\ORM\Mapping as ORM;

trait TCreatedAt
{
	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected ?\DateTimeImmutable $createdAt = null;

	public function getCreatedAt(): \DateTimeImmutable
	{
		return $this->createdAt;
	}

	/** @internal */
	#[ORM\PrePersist]
	public function setCreatedAt(): void
	{
		$this->createdAt = new \DateTimeImmutable();
	}
}
