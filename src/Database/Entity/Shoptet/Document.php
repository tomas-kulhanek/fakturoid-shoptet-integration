<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class Document
{
	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $code;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $paid = false;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $orderCode = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $addressesEqual = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $isValid;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $varSymbol;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $constSymbol = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $specSymbol = null;

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected DateTimeImmutable $creationTime;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $changeTime = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $dueDate = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	public ?int $billingMethodId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $billingMethodName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $vat;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $vatRate;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $toPay;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $currencyCode;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $withVat;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $withoutVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $exchangeRate = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $eshopBankAccount = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $eshopIban = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $eshopBic = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $eshopTaxMode = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $eshopDocumentRemark = null;

	#[ORM\Column(type: 'boolean', nullable: true)]
	protected ?bool $vatPayer = false;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $weight = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $completePackageWeight = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $externalSystemId = null;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $externalSystemLastSyncAt = null;

	/** @var ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem> */
	protected Collection|ArrayCollection $items;

	protected ?DocumentAddress $billingAddress = null;

	protected ?DocumentAddress $deliveryAddress = null;

	public function __construct(Project $project)
	{
		$this->project = $project;
		$this->items = new ArrayCollection();
	}

	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	public function setPaid(bool $paid): void
	{
		$this->paid = $paid;
	}

	public function setOrderCode(?string $orderCode): void
	{
		$this->orderCode = $orderCode;
	}

	public function setAddressesEqual(bool $addressesEqual): void
	{
		$this->addressesEqual = $addressesEqual;
	}

	public function setIsValid(bool $isValid): void
	{
		$this->isValid = $isValid;
	}

	public function setVarSymbol(int $varSymbol): void
	{
		$this->varSymbol = $varSymbol;
	}

	public function setConstSymbol(?string $constSymbol): void
	{
		$this->constSymbol = $constSymbol;
	}

	public function setSpecSymbol(?int $specSymbol): void
	{
		$this->specSymbol = $specSymbol;
	}

	public function setCreationTime(DateTimeImmutable $creationTime): void
	{
		$this->creationTime = $creationTime;
	}

	public function setChangeTime(?DateTimeImmutable $changeTime): void
	{
		$this->changeTime = $changeTime;
	}

	public function setDueDate(?DateTimeImmutable $dueDate): void
	{
		$this->dueDate = $dueDate;
	}

	public function setBillingMethodId(?int $billingMethodId): void
	{
		$this->billingMethodId = $billingMethodId;
	}

	public function setBillingMethodName(?string $billingMethodName): void
	{
		$this->billingMethodName = $billingMethodName;
	}

	public function setVat(?string $vat): void
	{
		$this->vat = $vat;
	}

	public function setVatRate(?string $vatRate): void
	{
		$this->vatRate = $vatRate;
	}

	public function setToPay(?string $toPay): void
	{
		$this->toPay = $toPay;
	}

	public function setCurrencyCode(?string $currencyCode): void
	{
		$this->currencyCode = $currencyCode;
	}

	public function setWithVat(?string $withVat): void
	{
		$this->withVat = $withVat;
	}

	public function setWithoutVat(?string $withoutVat): void
	{
		$this->withoutVat = $withoutVat;
	}

	public function setExchangeRate(?string $exchangeRate): void
	{
		$this->exchangeRate = $exchangeRate;
	}

	public function setEshopBankAccount(?string $eshopBankAccount): void
	{
		$this->eshopBankAccount = $eshopBankAccount;
	}

	public function setEshopIban(?string $eshopIban): void
	{
		$this->eshopIban = $eshopIban;
	}

	public function setEshopBic(?string $eshopBic): void
	{
		$this->eshopBic = $eshopBic;
	}

	public function setEshopTaxMode(?string $eshopTaxMode): void
	{
		$this->eshopTaxMode = $eshopTaxMode;
	}

	public function setEshopDocumentRemark(?string $eshopDocumentRemark): void
	{
		$this->eshopDocumentRemark = $eshopDocumentRemark;
	}

	public function setBillingAddress(?DocumentAddress $billingAddress): void
	{
		$this->billingAddress = $billingAddress;
	}

	public function setDeliveryAddress(?DocumentAddress $deliveryAddress): void
	{
		$this->deliveryAddress = $deliveryAddress;
	}

	public function setVatPayer(?bool $vatPayer): void
	{
		$this->vatPayer = $vatPayer;
	}

	public function setWeight(?string $weight): void
	{
		$this->weight = $weight;
	}

	public function setCompletePackageWeight(?string $completePackageWeight): void
	{
		$this->completePackageWeight = $completePackageWeight;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function isPaid(): bool
	{
		return $this->paid;
	}

	public function getOrderCode(): ?string
	{
		return $this->orderCode;
	}

	public function isAddressesEqual(): bool
	{
		return $this->addressesEqual;
	}

	public function isValid(): bool
	{
		return $this->isValid;
	}

	public function getVarSymbol(): int
	{
		return $this->varSymbol;
	}

	public function getConstSymbol(): ?string
	{
		return $this->constSymbol;
	}

	public function getSpecSymbol(): ?int
	{
		return $this->specSymbol;
	}

	public function getCreationTime(): DateTimeImmutable
	{
		return $this->creationTime;
	}

	public function getChangeTime(): ?DateTimeImmutable
	{
		return $this->changeTime;
	}

	public function getDueDate(): ?DateTimeImmutable
	{
		return $this->dueDate;
	}

	public function getBillingMethodId(): ?int
	{
		return $this->billingMethodId;
	}

	public function getBillingMethodName(): ?string
	{
		return $this->billingMethodName;
	}

	public function getVat(): ?string
	{
		return $this->vat;
	}

	public function getVatRate(): ?string
	{
		return $this->vatRate;
	}

	public function getToPay(): ?string
	{
		return $this->toPay;
	}

	public function getCurrencyCode(): ?string
	{
		return $this->currencyCode;
	}

	public function getWithVat(): ?string
	{
		return $this->withVat;
	}

	public function getWithoutVat(): ?string
	{
		return $this->withoutVat;
	}

	public function getExchangeRate(): ?string
	{
		return $this->exchangeRate;
	}

	public function getEshopBankAccount(): ?string
	{
		return $this->eshopBankAccount;
	}

	public function getEshopIban(): ?string
	{
		return $this->eshopIban;
	}

	public function getEshopBic(): ?string
	{
		return $this->eshopBic;
	}

	public function getEshopTaxMode(): ?string
	{
		return $this->eshopTaxMode;
	}

	public function getEshopDocumentRemark(): ?string
	{
		return $this->eshopDocumentRemark;
	}

	public function getBillingAddress(): ?DocumentAddress
	{
		return $this->billingAddress;
	}

	public function getDeliveryAddress(): ?DocumentAddress
	{
		return $this->deliveryAddress;
	}

	public function getVatPayer(): ?bool
	{
		return $this->vatPayer;
	}

	public function getWeight(): ?string
	{
		return $this->weight;
	}

	public function getCompletePackageWeight(): ?string
	{
		return $this->completePackageWeight;
	}

	/**
	 * @return ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem>
	 */
	public function getItems(): ArrayCollection|Collection
	{
		return $this->items;
	}

	/**
	 * @param ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem> $items
	 */
	public function setItems(ArrayCollection|Collection $items): void
	{
		$this->items = $items;
	}

	public function addItem(DocumentItem $documentItem): void
	{
		if (!$this->getItems()->contains($documentItem)) {
			$this->getItems()->add($documentItem);
		}
	}
}
