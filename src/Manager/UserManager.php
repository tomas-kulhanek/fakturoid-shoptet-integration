<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\Runtime\AuthenticationException;
use App\Security\Authenticator\UserAuthenticator;
use App\Security\Passwords;
use App\Security\SecurityUser;

class UserManager
{
	public function __construct(
		private SecurityUser $securityUser,
		private Passwords $passwords,
		private ProjectManager $projectManager,
		private EntityManager $entityManager
	) {
	}

	public function authenticate(string $eshopUrl, string $email, string $password): void
	{
		$project = $this->projectManager->getByEshopUrl($eshopUrl);

		$user = $project->getUsers()->filter(fn (User $user) => $user->getEmail() === $email)->first();
		if (!$user instanceof User) {
			throw new AuthenticationException('The username is incorrect.', UserAuthenticator::IDENTITY_NOT_FOUND);
		} elseif (!$this->passwords->verify($password, $user->getPassword())) {
			throw new AuthenticationException('The password is incorrect.', UserAuthenticator::INVALID_CREDENTIAL);
		} elseif ($this->passwords->needsRehash($user->getPassword())) {
			$user->setPassword($this->passwords->hash($password));
		}
		$this->entityManager->flush($user);

		$this->securityUser->login($user->toIdentity());
	}
}
