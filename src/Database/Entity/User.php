<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes\TCreatedAt;
use App\Database\Entity\Attributes\TGuid;
use App\Database\Entity\Attributes\TId;
use App\Database\Entity\Attributes\TUpdatedAt;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'core_user')]
#[ORM\HasLifecycleCallbacks]
class User extends AbstractEntity
{
	public const ROLE_ADMIN = 'admin';
	public const ROLE_USER = 'user';

	use TId;
	use TCreatedAt;
	use TUpdatedAt;
	use TGuid;

	#[ORM\Column(type: 'string', unique: true)]
	private string $email;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	private ?\DateTimeImmutable $lastLoggedAt = null;

	/** @var ArrayCollection<int, Project>|Collection<int, Project> */
	#[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'users')]
	protected Collection|ArrayCollection $projects;

	public function __construct(string $email, Project $project)
	{
		$this->projects = new ArrayCollection();
		$this->email = $email;
		$this->addProject($project);
		$project->addUser($this);
	}

	public function addProject(Project $project)
	{
		if (!$this->projects->contains($project)) {
			$this->projects->add($project);
		}
	}

	public function changeLoggedAt(): void
	{
		$this->lastLoggedAt = new \DateTimeImmutable();
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getLastLoggedAt(): ?\DateTimeImmutable
	{
		return $this->lastLoggedAt;
	}
}
