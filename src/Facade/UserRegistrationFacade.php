<?php

declare(strict_types=1);


namespace App\Facade;

use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\Logic\DuplicityException;
use App\Exception\Logic\NotFoundException;
use App\Mailing\MailBuilderFactory;
use App\Security\Passwords;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Nette\Mail\Message;

class UserRegistrationFacade
{
	public function __construct(
		private EntityManager $entityManager,
		private Passwords $passwords,
		private ComputerPasswordGenerator $computerPasswordGenerator,
		private MailBuilderFactory $mailBuilderFactory
	) {
	}

	public function findOneByEmailAndProject(string $email, Project $project): User
	{
		$userEntity = $this->entityManager->getUserRepository()->findOneByEmailAndProject($email, $project);
		if ($userEntity === null) {
			throw new NotFoundException();
		}
		return $userEntity;
	}

	public function createUser(string $email, Project $project): User
	{
		try {
			$this->findOneByEmailAndProject($email, $project);
			throw new DuplicityException();
		} catch (NotFoundException) {
		}
		$user = new User(
			email: $email,
			project: $project
		);
		$password = $this->computerPasswordGenerator->generatePassword();
		$user->setPassword($this->passwords->hash($password));
		$this->entityManager->persist($user);

		$message = $this->mailBuilderFactory->create();
		$message->setFrom('jsem@tomaskulhanek.cz');
		$message->setSubject('Mailik');
		$message->addTo($email);
		$message->setTemplateFile(__DIR__ . '/../resources/mail/installation.latte');
		$message->setParameters([
			'showAccounts' => false,
			'email' => $email,
			'password' => $password,
		]);
		$message->send();
		return $user;
	}
}
