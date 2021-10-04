<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Exception\Logic\NotFoundException;
use App\Facade\UserRegistrationFacade;
use Doctrine\ORM\EntityManagerInterface;

class ProjectManager
{
	public function __construct(
		private ClientInterface $apiDispatcher,
		private EntityManagerInterface $entityManager,
		private UserRegistrationFacade $userManager
	)
	{
	}

	public function getByEshopId(int $eshopId): Project
	{
		$project = $this->entityManager->getRepository(Project::class)
			->findOneBy(['eshopId' => $eshopId]);
		if (!$project instanceof Project) {
			throw new NotFoundException('Eshop was not found');
		}
		return $project;
	}

	public function confirmInstallation(string $code): Project
	{
		$installationData = $this->apiDispatcher->confirmInstallation($code);
		try {
			$project = $this->getByEshopId($installationData->eshopId);
		} catch (NotFoundException) {
			$project = new Project();
			$this->entityManager->persist($project);
		}
		$project->setAccessToken($installationData->access_token);
		$project->setContactEmail($installationData->contactEmail);
		$project->setEshopId($installationData->eshopId);
		$project->setEshopUrl($installationData->eshopUrl);
		$project->setScope($installationData->scope);
		$project->setTokenType($installationData->token_type);

		try {
			$userEntity = $this->userManager->findOneByEmail($installationData->contactEmail);
			$userEntity->addProject($project);
		} catch (NotFoundException) {

			try {
				$this->userManager->findOneByEmail($installationData->contactEmail);
				//todo co ted?
			} catch (NotFoundException) {
				$userEntity = new User(
					email: $installationData->contactEmail,
					project: $project
				);
				$project->setOwner($userEntity);
				$this->entityManager->persist($userEntity);
			}
		}

		$this->entityManager->flush();
		return $project;
		//odpalit do rabbita!
	}
}
