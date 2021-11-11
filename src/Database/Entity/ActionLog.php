<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\ActionLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ActionLogRepository::class)]
#[ORM\Table(name: 'sf_action_log')]
#[ORM\InheritanceType('SINGLE_TABLE')]
#[ORM\DiscriminatorColumn(name: 'document_type', type: 'string')]
#[ORM\DiscriminatorMap(['invoice' => InvoiceActionLog::class, 'proforma-invoice' => ProformaInvoiceActionLog::class, 'order' => OrderInvoiceActionLog::class, 'customer' => CustomerActionLog::class])]
#[ORM\HasLifecycleCallbacks]
abstract class ActionLog
{
	use Attributes\TId;
	use Attributes\TCreatedAt;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'user_id', nullable: true, onDelete: 'SET NULL')]
	protected ?User $user = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $type;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $errorCode = null;

	#[ORM\Column(type: 'text', nullable: true)]
	protected ?string $message = null;

	public function getProject(): Project
	{
		return $this->project;
	}

	public function setProject(Project $project): void
	{
		$this->project = $project;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setUser(?User $user): void
	{
		$this->user = $user;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function getMessage(): ?string
	{
		return $this->message;
	}

	public function setMessage(?string $message): void
	{
		$this->message = $message;
	}

	public function setErrorCode(?int $errorCode): void
	{
		$this->errorCode = $errorCode;
	}

	public function getErrorCode(): ?int
	{
		return $this->errorCode;
	}
}
