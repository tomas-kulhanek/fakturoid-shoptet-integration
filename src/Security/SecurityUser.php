<?php

declare(strict_types=1);

namespace App\Security;

use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Security\Authorizator\StaticAuthorizator;
use Nette\Security\Authorizator;
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

	/**
	 * @param mixed $resource
	 * @param mixed $privilege
	 */
	public function isAllowed($resource = Authorizator::ALL, $privilege = Authorizator::ALL): bool
	{
		$allowed = parent::isAllowed($resource, $privilege);
		if (!$allowed) {
			return false;
		}
		return match ($resource) {
			StaticAuthorizator::RESOURCE_PROFORMA_INVOICE => $this->getProjectEntity()->getSettings()->isShoptetSynchronizeProformaInvoices(),
			StaticAuthorizator::RESOURCE_INVOICE => $this->getProjectEntity()->getSettings()->isShoptetSynchronizeInvoices(),
			StaticAuthorizator::RESOURCE_CREDIT_NOTE => $this->getProjectEntity()->getSettings()->isShoptetSynchronizeCreditNotes(),
			StaticAuthorizator::RESOURCE_ORDER => $this->getProjectEntity()->getSettings()->isShoptetSynchronizeOrders(),
			default => true,
		};
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
