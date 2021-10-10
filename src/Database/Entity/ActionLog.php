<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\ActionLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ActionLogRepository::class)]
#[ORM\Table(name: 'core_action_log')]
#[ORM\HasLifecycleCallbacks]
class ActionLog
{
	use Attributes\TId;
	use Attributes\TCreatedAt;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $type;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $user;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $referenceId = null;

	public function __construct(
		Project         $project,
		string          $type,
		string          $user,
		string|int|null $referenceId
	) {
		$this->project = $project;
		$this->type = $type;
		$this->user = $user;
		if ($referenceId !== null) {
			$this->referenceId = (string) $referenceId;
		}
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function getUser(): string
	{
		return $this->user;
	}

	public function getReferenceId(): ?string
	{
		return $this->referenceId;
	}
}
