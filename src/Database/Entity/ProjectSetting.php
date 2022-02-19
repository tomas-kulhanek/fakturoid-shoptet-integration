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

	public const AUTOMATIZATION_MANUAL = 0;
	public const AUTOMATIZATION_AUTO = 2;

	public const AUTOMATIOZATIONS = [
		self::AUTOMATIZATION_MANUAL,
		self::AUTOMATIZATION_AUTO,
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

	#[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
	protected ?int $accountingNumberLineId = null;

	#[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
	protected ?int $accountingCreditNoteNumberLineId = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $accountingReminder = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $accountingUpdate = true;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingLanguage = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $propagateDeliveryAddress = false;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $automatization = self::AUTOMATIZATION_MANUAL;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $shoptetSynchronizeOrders = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $shoptetSynchronizeInvoices = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $shoptetSynchronizeProformaInvoices = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $shoptetSynchronizeCreditNotes = false;

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

	public function isAccountingReminder(): bool
	{
		return $this->accountingReminder;
	}

	public function setAccountingReminder(bool $accountingReminder): void
	{
		$this->accountingReminder = $accountingReminder;
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

	public function isShoptetSynchronizeOrders(): bool
	{
		return $this->shoptetSynchronizeOrders;
	}

	public function setShoptetSynchronizeOrders(bool $shoptetSynchronizeOrders): void
	{
		$this->shoptetSynchronizeOrders = $shoptetSynchronizeOrders;
	}

	public function isShoptetSynchronizeInvoices(): bool
	{
		return $this->shoptetSynchronizeInvoices;
	}

	public function setShoptetSynchronizeInvoices(bool $shoptetSynchronizeInvoices): void
	{
		$this->shoptetSynchronizeInvoices = $shoptetSynchronizeInvoices;
	}

	public function isShoptetSynchronizeProformaInvoices(): bool
	{
		return $this->shoptetSynchronizeProformaInvoices;
	}

	public function setShoptetSynchronizeProformaInvoices(bool $shoptetSynchronizeProformaInvoices): void
	{
		$this->shoptetSynchronizeProformaInvoices = $shoptetSynchronizeProformaInvoices;
	}

	public function isShoptetSynchronizeCreditNotes(): bool
	{
		return $this->shoptetSynchronizeCreditNotes;
	}

	public function setShoptetSynchronizeCreditNotes(bool $shoptetSynchronizeCreditNotes): void
	{
		$this->shoptetSynchronizeCreditNotes = $shoptetSynchronizeCreditNotes;
	}

	public function getAccountingNumberLineId(): ?int
	{
		return $this->accountingNumberLineId;
	}

	public function setAccountingNumberLineId(?int $accountingNumberLineId): void
	{
		$this->accountingNumberLineId = $accountingNumberLineId;
	}

	public function isAccountingUpdate(): bool
	{
		return $this->accountingUpdate;
	}

	public function setAccountingUpdate(bool $accountingUpdate): void
	{
		$this->accountingUpdate = $accountingUpdate;
	}

	public function getAccountingLanguage(): ?string
	{
		return $this->accountingLanguage;
	}

	public function setAccountingLanguage(?string $accountingLanguage): void
	{
		$this->accountingLanguage = $accountingLanguage;
	}

	public function getAccountingCreditNoteNumberLineId(): ?int
	{
		return $this->accountingCreditNoteNumberLineId;
	}

	public function setAccountingCreditNoteNumberLineId(?int $accountingCreditNoteNumberLineId): void
	{
		$this->accountingCreditNoteNumberLineId = $accountingCreditNoteNumberLineId;
	}
}
