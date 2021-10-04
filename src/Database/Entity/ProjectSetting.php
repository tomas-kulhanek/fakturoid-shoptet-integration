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
	public const AUTOMATIZATION_SEMI_AUTO = 1; // vytvari se i danove doklady/popr. proformy na zaklade zmeny stavu objednavky, ale neodesilaji se do Fakturoidu
	public const AUTOMATIZATION_AUTO = 2; // stejne jako AUTOMATIZATION_SEMI_AUTO, ale automaticky se posila i do Fakturoidu
	public const AUTOMATIOZATIONS = [
		self::AUTOMATIZATION_MANUAL,
		self::AUTOMATIZATION_AUTO,
		self::AUTOMATIZATION_SEMI_AUTO,
	];

	#[Orm\OneToOne(inversedBy: 'settings', targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $fakturoidEmail = null;

	#[ORM\Column(type: 'text', nullable: true)]
	protected ?string $fakturoidApiKey = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $fakturoidAccount = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $propagateDeliveryAddress = false;

	#[ORM\Column(type: 'string', nullable: false)]
	protected int $automatization = self::AUTOMATIZATION_MANUAL;

	public function __construct(Project $project)
	{
		$this->project = $project;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getFakturoidEmail(): ?string
	{
		return $this->fakturoidEmail;
	}

	public function setFakturoidEmail(?string $fakturoidEmail): void
	{
		$this->fakturoidEmail = $fakturoidEmail;
	}

	public function getFakturoidApiKey(): ?string
	{
		return $this->fakturoidApiKey;
	}

	public function setFakturoidApiKey(?string $fakturoidApiKey): void
	{
		$this->fakturoidApiKey = $fakturoidApiKey;
	}

	public function getFakturoidAccount(): ?string
	{
		return $this->fakturoidAccount;
	}

	public function setFakturoidAccount(?string $fakturoidAccount): void
	{
		$this->fakturoidAccount = $fakturoidAccount;
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
			$this->getFakturoidApiKey() !== null && $this->getFakturoidApiKey() !== ''
			&& $this->getFakturoidEmail() !== null && $this->getFakturoidEmail() !== ''
			&& $this->getFakturoidAccount() !== null && $this->getFakturoidAccount() !== '';
	}
}
