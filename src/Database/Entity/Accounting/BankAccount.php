<?php

declare(strict_types=1);


namespace App\Database\Entity\Accounting;

use App\Database\Entity\AbstractEntity;
use App\Database\Entity\Attributes;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\Accounting\BankAccountRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BankAccountRepository::class)]
#[ORM\Table(name: 'ac_bank_account')]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'project_accounting_id', fields: ['project', 'accountingId'])]
class BankAccount extends AbstractEntity
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	private Project $project;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	private bool $system = false;

	#[ORM\Column(type: 'integer', nullable: true)]
	private ?int $accountingId = null;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $name;

	#[ORM\Column(type: 'string', nullable: true)]
	private ?string $currency = null;

	#[ORM\Column(type: 'string', nullable: true)]
	private ?string $number = null;

	#[ORM\Column(type: 'string', nullable: true)]
	private ?string $iban = null;

	#[ORM\Column(type: 'string', nullable: true)]
	private ?string $swift = null;

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

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getCurrency(): ?string
	{
		return $this->currency;
	}

	public function setCurrency(?string $currency): void
	{
		$this->currency = $currency;
	}

	public function getNumber(): ?string
	{
		return $this->number;
	}

	public function setNumber(?string $number): void
	{
		$this->number = $number;
	}

	public function getIban(): ?string
	{
		return $this->iban;
	}

	public function setIban(?string $iban): void
	{
		$this->iban = $iban;
	}

	public function getSwift(): ?string
	{
		return $this->swift;
	}

	public function setSwift(?string $swift): void
	{
		$this->swift = $swift;
	}

	public function isSystem(): bool
	{
		return $this->system;
	}

	public function setSystem(bool $system): void
	{
		$this->system = $system;
	}
}
