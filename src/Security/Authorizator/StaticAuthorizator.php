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
		$this->addRole(User::ROLE_OWNER, [User::ROLE_USER, User::ROLE_ADMIN]);
		$this->addRole(User::ROLE_SUPERADMIN, [User::ROLE_USER, User::ROLE_ADMIN, User::ROLE_OWNER]);
	}

	/**
	 * Setup resources
	 */
	protected function addResources(): void
	{
		$this->addResource('App:Home');
		$this->addResource('Shoptet');
		$this->addResource('App:Order');
		$this->addResource('App:Invoice');
		$this->addResource('App:ProformaInvoice');
		$this->addResource('App:CreditNote');
		$this->addResource('App:Customer');
		$this->addResource('App:Settings');
		$this->addResource('App:Profile');
	}

	/**
	 * Setup ACL
	 */
	protected function addPermissions(): void
	{
		$this->allow(User::ROLE_USER, [
			'App:Home',
			'Shoptet',
			'App:Invoice',
			'App:ProformaInvoice',
			'App:CreditNote',
			'App:Profile',
		]);
		$this->allow(User::ROLE_ADMIN, [
			'App:Settings',
		]);
		$this->allow(User::ROLE_SUPERADMIN, [
			'App:Settings',
			'App:Order',
			'App:Customer',
		]);
	}
}
