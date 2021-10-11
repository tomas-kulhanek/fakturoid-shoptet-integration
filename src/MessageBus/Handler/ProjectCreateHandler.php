<?php declare(strict_types=1);


namespace App\MessageBus\Handler;


use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\DBAL\MultiDbConnectionWrapper;
use App\DTO\Shoptet\ConfirmInstallation;
use App\Exception\Logic\NotFoundException;
use App\Manager\ProjectManager;
use App\Security\SecretVault\ISecretVault;
use App\Service\ProjectCreateService;
use Doctrine\DBAL\Connection;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProjectCreateHandler implements MessageHandlerInterface
{
	/**
	 * @param ProjectCreateService $projectCreateService
	 * @param Connection|MultiDbConnectionWrapper $connection
	 */
	public function __construct(
		protected ProjectCreateService $projectCreateService,
		protected Connection           $connection,
		protected ProjectManager       $projectManager,
		protected EntityManager        $entityManager,
		protected ISecretVault         $secretVault
	)
	{
	}

	public function __invoke(ConfirmInstallation $installationData)
	{
		$this->projectCreateService->createNewProject($installationData);
		//todo zapsat info o projektu do hlavni databaze

		$this->connection->selectDatabase($installationData->eshopId);
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
		$project->setScope($installationData->scope);
		$project->setTokenType($installationData->token_type);

		$userEntity = new User(
			email: $installationData->contactEmail,
			project: $project
		);
		$userEntity->setRole(User::ROLE_OWNER);
		$this->entityManager->persist($userEntity);
		//todo zaslat jeste email
		$this->entityManager->flush();
	}
}
