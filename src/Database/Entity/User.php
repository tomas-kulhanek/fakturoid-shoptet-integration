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
#[ORM\UniqueConstraint(name: 'email_project', fields: ['email', 'project'])]
class User extends AbstractEntity
{
	public const ROLE_SUPERADMIN = 'superadmin';
	public const ROLE_USER = 'user';
	public const ROLE_ADMIN = 'admin';
	public const ROLE_OWNER = 'owner';

	use TId;
	use TCreatedAt;
	use TGuid;

	#[ORM\Column(type: 'string', unique: false, nullable: false)]
	private string $email;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $password;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $role = self::ROLE_USER;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $language = 'cs';

	#[ORM\Column(type: 'boolean', unique: false, nullable: false, options: ['default' => false])]
	private bool $forceChangePassword = false;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	private Project $project;

	public function __construct(string $email, Project $project)
	{
		$this->email = $email;
		$this->name = $email;
		$this->project = $project;
		$project->addUser($this);
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
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

	public function setEmail(string $email): void
	{
		$this->email = $email;
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function toIdentity(): Identity
	{
		return new Identity(
			$this->getId(),
			[$this->getRole()],
			[
				'project' => $this->getProject(),
				'user' => $this,
				'projectUrl' => $this->getProject()->getEshopUrl(),
				'projectId' => $this->getProject()->getEshopId(),
				'projectName' => $this->getProject()->getName(),
				'name' => $this->getName(),
				'email' => $this->getEmail(),
			]
		);
	}

	public function getLanguage(): string
	{
		return $this->language;
	}

	public function setLanguage(string $language): void
	{
		$this->language = $language;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function isForceChangePassword(): bool
	{
		return $this->forceChangePassword;
	}

	public function setForceChangePassword(bool $forceChangePassword): void
	{
		$this->forceChangePassword = $forceChangePassword;
	}
}
