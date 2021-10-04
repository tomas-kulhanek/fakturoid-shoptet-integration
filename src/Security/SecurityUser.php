<?php

declare(strict_types=1);

namespace App\Security;

use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use Nette\Security\Authorizator;
use Nette\Security\IAuthenticator;
use Nette\Security\IUserStorage;
use Nette\Security\User as NetteUser;
use Nette\Security\UserStorage;

/**
 * @method Identity getIdentity()
 */
final class SecurityUser extends NetteUser
{
	private ?User $user = null;
	private ?Project $project = null;

	public function __construct(
		IUserStorage $legacyStorage = null,
		IAuthenticator $authenticator = null,
		Authorizator $authorizator = null,
		UserStorage $storage = null,
		private EntityManager $entityManager
	) {
		parent::__construct($legacyStorage, $authenticator, $authorizator, $storage);
	}

	public function isAdmin(): bool
	{
		return $this->isInRole(User::ROLE_ADMIN);
	}

	public function getUserEntity(): ?User
	{
		if ($this->getIdentity()->getData()['userEntity'] instanceof User) {
			return $this->getIdentity()->getData()['userEntity'];
		}
		if (!$this->user instanceof User) {
			$this->user = $this->entityManager->getRepository(User::class)
				->findOneBy(['email' => $this->getIdentity()->getData()['email']]);
		}
		if (!$this->user instanceof User) {
			$this->logout(true);
		}
		return $this->user;
	}

	public function getProjectEntity(): ?Project
	{
		if ($this->getIdentity()->getData()['projectEntity'] instanceof Project) {
			return $this->getIdentity()->getData()['projectEntity'];
		}
		if (!$this->project instanceof Project) {
			$this->project = $this->entityManager->getRepository(Project::class)
				->findOneBy(['eshopId' => $this->getIdentity()->getData()['projectId']]);
		}
		if (!$this->project instanceof Project) {
			$this->logout(true);
		}
		return $this->project;
	}
}
