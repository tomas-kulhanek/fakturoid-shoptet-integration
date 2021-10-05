<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\Repository\Shoptet\ProjectRepository;
use App\Exception\Logic\NotFoundException;
use App\Facade\UserRegistrationFacade;
use App\Security\SecretVault\ISecretVault;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;
use Nette\Http\Url;

class ProjectManager
{
	public function __construct(
		private ClientInterface        $apiDispatcher,
		private EntityManagerInterface $entityManager,
		private UserRegistrationFacade $userManager,
		private ISecretVault           $secretVault
	) {
	}

	public function getRepository(): ProjectRepository
	{
		/** @var ProjectRepository $projectRepository */
		$projectRepository = $this->entityManager->getRepository(Project::class);
		return $projectRepository;
	}

	/**
	 * @return Collection<int, Project>
	 */
	public function getAllProjects(): Collection
	{
		$projects = $this->getRepository()
			->findAll();
		return new ArrayCollection($projects);
	}

	/**
	 * @return Collection<int, Project>
	 */
	public function getAllActiveProjects(): Collection
	{
		$projects = $this->getRepository()
			->createQueryBuilder('p')
			->addSelect('ps')
			->leftJoin('p.setting', 'ps')
			->where('p.state = :state')
			->setParameter('state', Project::STATE_ACTIVE)
			->getQuery()->getArrayResult();

		return new ArrayCollection($projects);
	}

	public function getByEshopUrl(string $eshopUrl): Project
	{
		$url = new Url();
		$url->setScheme('https');
		$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $eshopUrl));
		$clonedUrl = clone $url;
		$clonedUrl->setScheme('http');
		$qb = $this->entityManager->getRepository(Project::class)
			->createQueryBuilder('p');
		try {
			$project = $qb
				->where($qb->expr()->like('p.eshopUrl', ':eshopUrl'))
				->orWhere($qb->expr()->like('p.eshopUrl', ':eshopUrl2'))
				->setParameter('eshopUrl', $url->getAbsoluteUrl())
				->setParameter('eshopUrl2', $clonedUrl->getAbsoluteUrl())
				->getQuery()->getSingleResult();
		} catch (NoResultException) {
			throw new NotFoundException('Eshop was not found');
		}
		return $project;
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
