<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\InvoiceEETRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: InvoiceEETRepository::class)]
#[ORM\Table(name: 'sf_invoice_eet')]
#[ORM\HasLifecycleCallbacks]
class InvoiceEET
{
	use Attributes\TId;

	#[ORM\OneToOne(targetEntity: Invoice::class)]
	#[ORM\JoinColumn(name: 'invoice_id', nullable: false, onDelete: 'CASCADE')]
	protected Invoice $invoice;

	#[ORM\Column(type: 'string', unique: false, nullable: true)]
	protected ?string $uuid = null;

	#[ORM\Column(type: 'integer', unique: false, nullable: true)]
	protected ?int $accountingId = null;

	#[ORM\Column(type: 'boolean', unique: false, nullable: false)]
	protected bool $firstSent = false;

	#[ORM\Column(type: 'string', unique: false, nullable: false)]
	protected string $vatId;

	#[ORM\Column(type: 'datetime_immutable', unique: false, nullable: true)]
	protected ?\DateTimeImmutable $revenueDate = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $totalRevenue = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $vatBase1 = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $vat1 = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $vatBase2 = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $vat2 = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $vatBase3 = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $vat3 = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $nonTaxableBase = null;

	#[ORM\Column(type: 'float', unique: false, nullable: true)]
	protected ?float $exchangeRate = null;

	#[ORM\Column(type: 'string', unique: false, nullable: true)]
	protected ?string $pkp = null;

	#[ORM\Column(type: 'string', unique: false, nullable: true)]
	protected ?string $bkp = null;

	#[ORM\Column(type: 'string', unique: false, nullable: true)]
	protected ?string $fik = null;

	#[ORM\Column(name:'`mode`', type: 'integer', unique: false, nullable: false)]
	protected int $mode = 0;

	#[ORM\Column(type: 'string', unique: false, nullable: false)]
	protected string $eetMod = 'Sandbox';

	#[ORM\Column(type: 'datetime_immutable', unique: false, nullable: true)]
	protected ?\DateTimeImmutable $sent = null;

	#[ORM\Column(type: 'string', unique: false, nullable: false)]
	protected string $cashDeskId;

	#[ORM\Column(type: 'string', unique: false, nullable: false)]
	protected string $documentType;

	#[ORM\Column(name:'`active`', type: 'boolean', unique: false, nullable: false)]
	protected bool $active = false;

	public function __construct(Invoice $invoice)
	{
		$this->invoice = $invoice;
	}

	public function getUuid(): ?string
	{
		return $this->uuid;
	}

	public function setUuid(?string $uuid): void
	{
		$this->uuid = $uuid;
	}

	public function isFirstSent(): bool
	{
		return $this->firstSent;
	}

	public function setFirstSent(bool $firstSent): void
	{
		$this->firstSent = $firstSent;
	}

	public function getVatId(): string
	{
		return $this->vatId;
	}

	public function setVatId(string $vatId): void
	{
		$this->vatId = $vatId;
	}

	public function getRevenueDate(): ?\DateTimeImmutable
	{
		return $this->revenueDate;
	}

	public function setRevenueDate(?\DateTimeImmutable $revenueDate): void
	{
		$this->revenueDate = $revenueDate;
	}

	public function getTotalRevenue(): ?float
	{
		return $this->totalRevenue;
	}

	public function setTotalRevenue(?float $totalRevenue): void
	{
		$this->totalRevenue = $totalRevenue;
	}

	public function getVatBase1(): ?float
	{
		return $this->vatBase1;
	}

	public function setVatBase1(?float $vatBase1): void
	{
		$this->vatBase1 = $vatBase1;
	}

	public function getVat1(): ?float
	{
		return $this->vat1;
	}

	public function setVat1(?float $vat1): void
	{
		$this->vat1 = $vat1;
	}

	public function getVatBase2(): ?float
	{
		return $this->vatBase2;
	}

	public function setVatBase2(?float $vatBase2): void
	{
		$this->vatBase2 = $vatBase2;
	}

	public function getVat2(): ?float
	{
		return $this->vat2;
	}

	public function setVat2(?float $vat2): void
	{
		$this->vat2 = $vat2;
	}

	public function getVatBase3(): ?float
	{
		return $this->vatBase3;
	}

	public function setVatBase3(?float $vatBase3): void
	{
		$this->vatBase3 = $vatBase3;
	}

	public function getVat3(): ?float
	{
		return $this->vat3;
	}

	public function setVat3(?float $vat3): void
	{
		$this->vat3 = $vat3;
	}

	public function getNonTaxableBase(): ?float
	{
		return $this->nonTaxableBase;
	}

	public function setNonTaxableBase(?float $nonTaxableBase): void
	{
		$this->nonTaxableBase = $nonTaxableBase;
	}

	public function getExchangeRate(): ?float
	{
		return $this->exchangeRate;
	}

	public function setExchangeRate(?float $exchangeRate): void
	{
		$this->exchangeRate = $exchangeRate;
	}

	public function getPkp(): ?string
	{
		return $this->pkp;
	}

	public function setPkp(?string $pkp): void
	{
		$this->pkp = $pkp;
	}

	public function getBkp(): ?string
	{
		return $this->bkp;
	}

	public function setBkp(?string $bkp): void
	{
		$this->bkp = $bkp;
	}

	public function getFik(): ?string
	{
		return $this->fik;
	}

	public function setFik(?string $fik): void
	{
		$this->fik = $fik;
	}

	public function getMode(): int
	{
		return $this->mode;
	}

	public function setMode(int $mode): void
	{
		$this->mode = $mode;
	}

	public function getEetMod(): string
	{
		return $this->eetMod;
	}

	public function setEetMod(string $eetMod): void
	{
		$this->eetMod = $eetMod;
	}

	public function getSent(): ?\DateTimeImmutable
	{
		return $this->sent;
	}

	public function setSent(?\DateTimeImmutable $sent): void
	{
		$this->sent = $sent;
	}

	public function getCashDeskId(): string
	{
		return $this->cashDeskId;
	}

	public function setCashDeskId(string $cashDeskId): void
	{
		$this->cashDeskId = $cashDeskId;
	}

	public function getDocumentType(): string
	{
		return $this->documentType;
	}

	public function setDocumentType(string $documentType): void
	{
		$this->documentType = $documentType;
	}

	public function isActive(): bool
	{
		return $this->active;
	}

	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

	public function getAccountingId(): ?int
	{
		return $this->accountingId;
	}

	public function setAccountingId(?int $accountingId): void
	{
		$this->accountingId = $accountingId;
	}

}
