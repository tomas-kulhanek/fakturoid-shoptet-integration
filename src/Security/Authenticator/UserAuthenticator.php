<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\Runtime\AuthenticationException;
use App\Security\Identity;
use App\Security\Passwords;
use Nette\Security;
use Nette\Security\IIdentity;

final class UserAuthenticator implements Security\Authenticator, Security\IdentityHandler
{
	public function __construct(
		private EntityManager $em,
		private Passwords     $passwords
	) {
	}

	public function sleepIdentity(IIdentity $identity): IIdentity
	{
		return new Identity($identity->getId());
	}

	public function wakeupIdentity(IIdentity $identity): ?IIdentity
	{
		/** @var User|null $user */
		$user = $this->em->getUserRepository()->createQueryBuilder('u')
			->addSelect('p')
			->addSelect('ps')
			->innerJoin('u.project', 'p')
			->innerJoin('p.settings', 'ps')
			->where('u.id = :userId')
			->setParameter('userId', $identity->getId())
			->getQuery()->getSingleResult();

		return $user !== null ? $this->createIdentity($user) : null;
	}

	/**
	 * @throws AuthenticationException
	 */
	public function authenticate(string $username, string $password): IIdentity
	{
		/** @var User|null $user */
		$user = $this->em->getUserRepository()->findOneBy(['email' => $username]);

		if (!$user instanceof User) {
			throw new AuthenticationException('The username is incorrect.', self::IDENTITY_NOT_FOUND);
		} elseif (!$this->passwords->verify($password, $user->getPassword())) {
			throw new AuthenticationException('The password is incorrect.', self::INVALID_CREDENTIAL);
		}
		if ($user->getProject()->isSuspended()) {
			throw new AuthenticationException('Your project is suspended', self::INVALID_CREDENTIAL);
		}

		$this->em->flush();

		return $this->createIdentity($user);
	}

	protected function createIdentity(User $user): IIdentity
	{
		return $user->toIdentity();
	}
}
