<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes\TCreatedAt;
use App\Database\Entity\Attributes\TGuid;
use App\Database\Entity\Attributes\TId;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\UserRepository;
use App\Security\Identity;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'core_user')]
#[ORM\HasLifecycleCallbacks]
class User extends AbstractEntity
{
	public const ROLE_SUPERADMIN = 'superadmin';
	public const ROLE_USER = 'user';
	public const ROLE_ADMIN = 'admin';
	public const ROLE_OWNER = 'owner';

	use TId;
	use TCreatedAt;
	use TGuid;

	#[ORM\Column(type: 'string', unique: true, nullable: false)]
	private string $email;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $password;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $role = self::ROLE_USER;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	private Project $project;

	public function __construct(string $email, Project $project)
	{
		$this->email = $email;
		$this->project = $project;
		$project->addUser($this);
	}

	public function getPassword(): string
	{
		return $this->password;
	}

	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	public function getRole(): string
	{
		return $this->role;
	}

	public function setRole(string $role): void
	{
		$this->role = $role;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function toIdentity()
	{
		return new Identity(
			$this->getId(),
			[$this->getRole()],
			[
				'projectUrl' => $this->getProject()->getEshopUrl(),
				'projectId' => $this->getProject()->getEshopId(),
				'projectName' => $this->getProject()->getEshopHost(), //todo
				'name' => $this->getEmail(),
				'email' => $this->getEmail(),
			]
		);
	}

	public function getProject(): Project
	{
		return $this->project;
	}


}
