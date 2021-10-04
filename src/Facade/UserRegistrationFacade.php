<?php

declare(strict_types=1);


namespace App\Facade;

use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\Logic\DuplicityException;
use App\Exception\Logic\NotFoundException;
use App\Security\Passwords;

class UserRegistrationFacade
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	public function findOneByEmail(string $email): User
	{
		$userEntity = $this->entityManager->getUserRepository()->findOneByEmail($email);
		if ($userEntity === null) {
			throw new NotFoundException();
		}
		return $userEntity;
	}

	public function createUser(string $email, Project $project): User
	{
		// todo
		try {
			$this->findOneByEmail($email);
			throw new DuplicityException();
		} catch (NotFoundException) {
		}
		$user = new User(
			email: $email,
			project: $project
		);
		$this->entityManager->persist($user);
		$this->entityManager->flush($user);

		return $user;
	}
}
