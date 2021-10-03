<?php

declare(strict_types=1);

namespace App\Security;

use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use Nette\Security\SimpleIdentity as NetteIdentity;

class Identity extends NetteIdentity
{
	public function getFullName(): string
	{
		return $this->getEntity()->getFullname();
	}

	public function getEntity(): ?User
	{
		return $this->getData()['user'] ?? null;
	}

	public function getProject(): ?Project
	{
		return $this->getData()['eshop'] ?? null;
	}
}
