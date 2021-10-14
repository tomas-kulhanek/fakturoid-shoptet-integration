<?php

declare(strict_types=1);

namespace App\Security;

use App\Database\Entity\Shoptet\Project;
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

	public function getUserEntity(): ?User
	{
		return $this->getIdentity()->getData()['user'];
	}

	public function getProjectEntity(): ?Project
	{
		return $this->getIdentity()->getData()['project'];
	}
}
