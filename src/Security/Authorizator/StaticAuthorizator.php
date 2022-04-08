<?php

declare(strict_types=1);

namespace App\Security\Authorizator;

use App\Database\Entity\User;
use Nette\Security\Permission;

final class StaticAuthorizator extends Permission
{
	public const RESOURCE_HOME = 'App:Home';
	public const RESOURCE_SHOPTET = 'Shoptet';
	public const RESOURCE_ORDER = 'App:Order';
	public const RESOURCE_INVOICE = 'App:Invoice';
	public const RESOURCE_PROFORMA_INVOICE = 'App:ProformaInvoice';
	public const RESOURCE_CREDIT_NOTE = 'App:CreditNote';
	public const RESOURCE_CUSTOMER = 'App:Customer';
	public const RESOURCE_SETTINGS = 'App:Settings';
	public const RESOURCE_PROFILE = 'App:Profile';

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
		$this->addResource(self::RESOURCE_HOME);
		$this->addResource(self::RESOURCE_SHOPTET);
		$this->addResource(self::RESOURCE_ORDER);
		$this->addResource(self::RESOURCE_INVOICE);
		$this->addResource(self::RESOURCE_PROFORMA_INVOICE);
		$this->addResource(self::RESOURCE_CREDIT_NOTE);
		$this->addResource(self::RESOURCE_CUSTOMER);
		$this->addResource(self::RESOURCE_SETTINGS);
		$this->addResource(self::RESOURCE_PROFILE);
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
			'App:Order',
			'App:ProformaInvoice',
			'App:CreditNote',
			'App:Profile',
		]);
		$this->allow(User::ROLE_ADMIN, [
			'App:Settings',
		]);
		$this->allow(User::ROLE_SUPERADMIN, [
			'App:Settings',
			//
			'App:Customer',
		]);
	}
}
