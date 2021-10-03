<?php

declare(strict_types=1);

namespace App\Database\Entity\Attributes;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait TGuid
{
	#[ORM\Column(type: 'uuid', unique: true, nullable: false)]
	protected UuidInterface $guid;

	public function getGuid(): UuidInterface
	{
		return $this->guid;
	}

	/** @internal */
	#[ORM\PrePersist]
	public function setGuid(): void
	{
		$this->guid = Uuid::uuid4();
	}
}
