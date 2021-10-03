<?php

declare(strict_types=1);

namespace App\Security\Authorizator;

use App\Database\Entity\User;
use Nette\Security\Permission;

final class StaticAuthorizator extends Permission
{
	/**
	 * Create ACL
	 */
	public function __construct()
	{
		$this->addRoles();
		$this->addResources();
		$this->addPermissions();
	}

	/**
	 * Setup roles
	 */
	protected function addRoles(): void
	{
		$this->addRole(User::ROLE_USER);
		$this->addRole(User::ROLE_ADMIN, [User::ROLE_USER]);
	}

	/**
	 * Setup resources
	 */
	protected function addResources(): void
	{
		$this->addResource('App:Home');
	}

	/**
	 * Setup ACL
	 */
	protected function addPermissions(): void
	{
		$this->allow(User::ROLE_USER, [
			'App:Home',
		]);
	}
}
