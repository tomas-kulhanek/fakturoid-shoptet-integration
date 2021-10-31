<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\DTO\Shoptet\ConfirmInstallation;
use App\Exception\Logic\NotFoundException;
use App\Facade\UserRegistrationFacade;
use App\Manager\ProjectManager;
use App\Security\SecretVault\ISecretVault;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProjectCreateHandler implements MessageHandlerInterface
{
	private const SUPERADMIN_MAIL = 'jsem@tomaskulhanek.cz';

	public function __construct(
		protected ProjectManager $projectManager,
		protected EntityManager $entityManager,
		protected ISecretVault $secretVault,
		protected UserRegistrationFacade $userRegistrationFacade
	) {
	}

	public function __invoke(ConfirmInstallation $installationData): void
	{
		try {
			$project = $this->projectManager->getByEshopId($installationData->eshopId);
		} catch (NotFoundException) {
			$project = new Project();
			$this->entityManager->persist($project);
			$projectSetting = new ProjectSetting($project);
			$this->entityManager->persist($projectSetting);
		}
		$project->setAccessToken(
			$this->secretVault->encrypt($installationData->access_token)
		);
		$project->setContactEmail($installationData->contactEmail);
		$project->setEshopId($installationData->eshopId);
		$project->setEshopUrl($installationData->eshopUrl);
		$project->setName($installationData->eshopUrl);
		$project->setScope($installationData->scope);
		$project->setTokenType($installationData->token_type);

		$userEntity = $this->userRegistrationFacade->createUser($installationData->contactEmail, $project);
		$userEntity->setRole(
			$installationData->contactEmail === self::SUPERADMIN_MAIL ? User::ROLE_SUPERADMIN : User::ROLE_OWNER
		);
		$userEntity->setForceChangePassword(true);
		$this->entityManager->persist($userEntity);

		if ($installationData->contactEmail !== self::SUPERADMIN_MAIL) {
			$userEntity2 = $this->userRegistrationFacade->createUser(self::SUPERADMIN_MAIL, $project);
			$userEntity2->setRole(User::ROLE_SUPERADMIN);
			$userEntity2->setForceChangePassword(true);
			$this->entityManager->persist($userEntity2);
		}

		$this->entityManager->flush();
	}
}
