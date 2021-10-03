<?php

declare(strict_types=1);

namespace App\Security;

use App\Database\Entity\User;
use Nette\Security\User as NetteUser;

/**
 * @method Identity getIdentity()
 */
final class SecurityUser extends NetteUser
{
	public function isAdmin(): bool
	{
		return $this->isInRole(User::ROLE_ADMIN);
	}

	public function getEntity(): ?User
	{
		return $this->getIdentity()->getData()['user'] ?? null;
	}
}
