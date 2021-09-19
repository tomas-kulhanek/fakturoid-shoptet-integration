<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\Runtime\AuthenticationException;
use App\Security\Passwords;
use Nette\Security;
use Nette\Security\IIdentity;

final class UserAuthenticator implements Security\Authenticator, Security\IdentityHandler
{
	public function __construct(
		private EntityManager $em,
		private Passwords $passwords
	) {
	}

	public function sleepIdentity(IIdentity $identity): IIdentity
	{
		return $identity;
	}

	public function wakeupIdentity(IIdentity $identity): ?IIdentity
	{
		/** @var User|null $user */
		$user = $this->em->getUserRepository()->findOneBy(['id' => $identity->getId()]);

		return $user !== null ? $this->createIdentity($user) : null;
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(string $username, string $password): IIdentity
	{
		$user = $this->em->getUserRepository()->findOneBy(['email' => $username]);

		if ($user === null) {
			throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!$user->isActivated()) {
			throw new AuthenticationException('The user is not active.', self::INVALID_CREDENTIAL);
		} elseif (!$this->passwords->verify($password, $user->getPasswordHash())) {
			throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}

		$user->changeLoggedAt();
		$this->em->flush();

		return $this->createIdentity($user);
	}

	protected function createIdentity(User $user): IIdentity
	{
		return $user->toIdentity();
	}
}
