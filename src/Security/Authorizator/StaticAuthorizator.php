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
		$this->addResource('Shoptet:Order');
		$this->addResource('Shoptet:Invoice');
		$this->addResource('Shoptet:ProformaInvoice');
		$this->addResource('Shoptet:CreditNote');
		$this->addResource('Shoptet:Customer');
		$this->addResource('App:Settings');
	}

	/**
	 * Setup ACL
	 */
	protected function addPermissions(): void
	{
		$this->allow(User::ROLE_USER, [
			'App:Home',
			'Shoptet',
			'Shoptet:Order',
			'Shoptet:Invoice',
			'Shoptet:ProformaInvoice',
			'Shoptet:CreditNote',
			'Shoptet:Customer',
		]);
		$this->allow(User::ROLE_ADMIN, [
			'App:Settings',
		]);
	}
}
