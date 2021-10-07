<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\ProjectSettingRepository;
use App\Exception\LogicException;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ProjectSettingRepository::class)]
#[ORM\Table(name: 'sf_project_setting')]
#[ORM\HasLifecycleCallbacks]
class ProjectSetting
{
	use Attributes\TId;
	use Attributes\TUpdatedAt;

	public const AUTOMATIZATION_MANUAL = 0; // jen se vytvari objednavky a vse si musi uzivatel obstarat sam
	public const AUTOMATIZATION_SEMI_AUTO = 1; // vytvari se i danove doklady/popr. proformy na zaklade zmeny stavu objednavky, ale neodesilaji se do Accountingu
	public const AUTOMATIZATION_AUTO = 2; // stejne jako AUTOMATIZATION_SEMI_AUTO, ale automaticky se posila i do Accountingu
	public const AUTOMATIOZATIONS = [
		self::AUTOMATIZATION_MANUAL,
		self::AUTOMATIZATION_AUTO,
		self::AUTOMATIZATION_SEMI_AUTO,
	];

	#[Orm\OneToOne(inversedBy: 'settings', targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingEmail = null;

	#[ORM\Column(type: 'text', nullable: true)]
	protected ?string $accountingApiKey = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingAccount = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $propagateDeliveryAddress = false;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $automatization = self::AUTOMATIZATION_MANUAL;

	public function __construct(Project $project)
	{
		$this->project = $project;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getAccountingEmail(): ?string
	{
		return $this->accountingEmail;
	}

	public function setAccountingEmail(?string $accountingEmail): void
	{
		$this->accountingEmail = $accountingEmail;
	}

	public function getAccountingApiKey(): ?string
	{
		return $this->accountingApiKey;
	}

	public function setAccountingApiKey(?string $accountingApiKey): void
	{
		$this->accountingApiKey = $accountingApiKey;
	}

	public function getAccountingAccount(): ?string
	{
		return $this->accountingAccount;
	}

	public function setAccountingAccount(?string $accountingAccount): void
	{
		$this->accountingAccount = $accountingAccount;
	}

	public function getAutomatization(): int
	{
		return $this->automatization;
	}

	public function setAutomatization(int $automatization): void
	{
		if (!in_array($automatization, self::AUTOMATIOZATIONS, true)) {
			throw new LogicException();
		}
		$this->automatization = $automatization;
	}

	public function isPropagateDeliveryAddress(): bool
	{
		return $this->propagateDeliveryAddress;
	}

	public function setPropagateDeliveryAddress(bool $propagateDeliveryAddress): void
	{
		$this->propagateDeliveryAddress = $propagateDeliveryAddress;
	}

	public function isSetRight(): bool
	{
		return
			$this->getAccountingApiKey() !== null && $this->getAccountingApiKey() !== ''
			&& $this->getAccountingEmail() !== null && $this->getAccountingEmail() !== ''
			&& $this->getAccountingAccount() !== null && $this->getAccountingAccount() !== '';
	}
}
