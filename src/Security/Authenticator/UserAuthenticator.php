<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\Runtime\AuthenticationException;
use App\Security\Identity;
use Nette\Security;
use Nette\Security\IIdentity;

final class UserAuthenticator implements Security\Authenticator, Security\IdentityHandler
{
	public function __construct(
		private EntityManager $em
	) {
	}

	public function sleepIdentity(IIdentity $identity): IIdentity
	{
		$identityData = $identity->getData();
		unset($identityData['projectEntity']);

		return new Identity($identity->getId(), $identity->getRoles(), $identityData);
	}

	public function wakeupIdentity(IIdentity $identity): ?IIdentity
	{
		$identityData = $identity->getData();
		$userEntity = $this->em->getRepository(User::class)->findOneBy(['id' => $identity->getId()]);
		if (!$userEntity instanceof User) {
			throw new AuthenticationException();
		}
		$project = $this->em->getRepository(Project::class)->findOneBy(['eshopId' => $identityData['projectId']]);
		if (!$project instanceof Project) {
			throw new AuthenticationException();
		}
		return new Identity($identity->getId(), [$userEntity->getRole()], array_merge($identityData, ['projectEntity' => $project]));
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(string $username, string $password): IIdentity
	{
		throw new AuthenticationException();
	}
}
