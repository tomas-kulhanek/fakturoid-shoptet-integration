<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Accounting\NumberLine;
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

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingCode = null;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?\DateTimeImmutable $accountingLastHookUsedAt = null;

	#[ORM\Column(type: 'text', nullable: true)]
	protected ?string $accountingApiKey = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingAccount = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $accountingReminder = false;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => true])]
	protected bool $accountingMarkInvoiceAsPaid = true;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingInvoiceTags = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingProformaInvoiceTags = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingCreditNoteTags = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected ?string $accountingCustomerTags = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $accountingUpdate = true;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingLanguage = null;

	#[ORM\Column(type: 'string', nullable: true, options: ['default' => 'CZK'])]
	protected string $accountingDefaultCurrency = 'CZK';

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

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	protected bool $accountingSendMailInvoice = false;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	protected bool $accountingSendMailProformaInvoice = false;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	protected bool $accountingSendRepeatedlyMailInvoice = false;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	protected bool $accountingSendRepeatedlyMailProformaInvoice = false;

	#[Orm\OneToOne(targetEntity: NumberLine::class)]
	#[ORM\JoinColumn(name: 'accounting_number_line', referencedColumnName: 'id', onDelete: 'SET NULL')]
	protected ?NumberLine $accountingNumberLine = null;

	#[Orm\OneToOne(targetEntity: NumberLine::class)]
	#[ORM\JoinColumn(name: 'accounting_credit_note_number_line', referencedColumnName: 'id', onDelete: 'SET NULL')]
	protected ?NumberLine $accountingCreditNoteNumberLine = null;

	#[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
	protected ?int $accountingNumberLineId = null;

	#[ORM\Column(type: 'integer', nullable: true, options: ['default' => null])]
	protected ?int $accountingCreditNoteNumberLineId = null;

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
		if ($accountingEmail !== null && trim($accountingEmail) !== '') {
			$this->accountingCode = sha1($accountingEmail);
		} else {
			$this->accountingCode = null;
		}
	}

	public function getAccountingCode(): ?string
	{
		return $this->accountingCode;
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

	public function getAccountingInvoiceTags(): ?string
	{
		return $this->accountingInvoiceTags;
	}

	public function setAccountingInvoiceTags(?string $accountingInvoiceTags): void
	{
		$this->accountingInvoiceTags = str_replace(['https://', 'http://', '/'], ['', '', ''], $accountingInvoiceTags);
	}

	public function getAccountingProformaInvoiceTags(): ?string
	{
		return $this->accountingProformaInvoiceTags;
	}

	public function setAccountingProformaInvoiceTags(?string $accountingProformaInvoiceTags): void
	{
		$this->accountingProformaInvoiceTags = str_replace(['https://', 'http://', '/'], ['', '', ''], $accountingProformaInvoiceTags);
	}

	public function getAccountingCreditNoteTags(): ?string
	{
		return $this->accountingCreditNoteTags;
	}

	public function setAccountingCreditNoteTags(?string $accountingCreditNoteTags): void
	{
		$this->accountingCreditNoteTags = str_replace(['https://', 'http://', '/'], ['', '', ''], $accountingCreditNoteTags);
	}

	public function getAccountingCustomerTags(): ?string
	{
		return $this->accountingCustomerTags;
	}

	public function setAccountingCustomerTags(?string $accountingCustomerTags): void
	{
		$this->accountingCustomerTags = str_replace(['https://', 'http://', '/'], ['', '', ''], $accountingCustomerTags);
	}

	public function isAccountingMarkInvoiceAsPaid(): bool
	{
		return $this->accountingMarkInvoiceAsPaid;
	}

	public function setAccountingMarkInvoiceAsPaid(bool $accountingMarkInvoiceAsPaid): void
	{
		$this->accountingMarkInvoiceAsPaid = $accountingMarkInvoiceAsPaid;
	}

	public function isAccountingSendMailInvoice(): bool
	{
		return $this->accountingSendMailInvoice;
	}

	public function isAccountingSendMailProformaInvoice(): bool
	{
		return $this->accountingSendMailProformaInvoice;
	}

	public function isAccountingSendRepeatedlyMailInvoice(): bool
	{
		return $this->accountingSendRepeatedlyMailInvoice;
	}

	public function isAccountingSendRepeatedlyMailProformaInvoice(): bool
	{
		return $this->accountingSendRepeatedlyMailProformaInvoice;
	}

	public function getAccountingNumberLine(): ?NumberLine
	{
		return $this->accountingNumberLine;
	}

	public function setAccountingNumberLine(?NumberLine $accountingNumberLine): void
	{
		$this->accountingNumberLine = $accountingNumberLine;
	}

	public function getAccountingCreditNoteNumberLine(): ?NumberLine
	{
		return $this->accountingCreditNoteNumberLine;
	}

	public function setAccountingCreditNoteNumberLine(?NumberLine $accountingCreditNoteNumberLine): void
	{
		$this->accountingCreditNoteNumberLine = $accountingCreditNoteNumberLine;
	}

	public function getAccountingDefaultCurrency(): string
	{
		return $this->accountingDefaultCurrency;
	}

	public function setAccountingDefaultCurrency(string $currency): void
	{
		$this->accountingDefaultCurrency = $currency;
	}

	public function setAccountingLastHookUsedAt(?\DateTimeImmutable $accountingLastHookUsedAt): void
	{
		$this->accountingLastHookUsedAt = $accountingLastHookUsedAt;
	}
}
