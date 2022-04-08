<?php

declare(strict_types=1);


namespace App\Database\Entity\Accounting;

use App\Database\Entity\AbstractEntity;
use App\Database\Entity\Attributes;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\Accounting\NumberLineRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NumberLineRepository::class)]
#[ORM\Table(name: 'ac_number_lines')]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'project_accounting_id', fields: ['project', 'accountingId'])]
class NumberLine extends AbstractEntity
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	private Project $project;

	#[ORM\Column(type: 'integer', nullable: true)]
	private ?int $accountingId = null;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $format;

	#[ORM\Column(type: 'string', nullable: true)]
	private ?string $preview = null;

	#[ORM\Column(name: '`default`', type: 'boolean', nullable: false)]
	private bool $default = false;

	public function __construct(Project $project)
	{
		$this->project = $project;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getAccountingId(): ?int
	{
		return $this->accountingId;
	}

	public function setAccountingId(?int $accountingId): void
	{
		$this->accountingId = $accountingId;
	}

	public function getFormat(): string
	{
		return $this->format;
	}

	public function setFormat(string $format): void
	{
		$this->format = $format;
	}

	public function isDefault(): bool
	{
		return $this->default;
	}

	public function setDefault(bool $default): void
	{
		$this->default = $default;
	}

	public function getPreview(): ?string
	{
		return $this->preview;
	}

	public function setPreview(?string $preview): void
	{
		$this->preview = $preview;
	}
}
