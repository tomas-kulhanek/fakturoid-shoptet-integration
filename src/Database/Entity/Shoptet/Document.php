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
	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $shoptetCode = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $paid = false;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $orderCode = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $addressesEqual = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $isValid;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $varSymbol = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $constSymbol = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $specSymbol = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $companyId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $vatId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $taxId = null;

	#[ORM\Column(type: 'boolean', nullable: true)]
	protected ?bool $vatPayer = false;

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

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $vat;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $vatRate;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $toPay;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $currencyCode;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $withVat;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $withoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $exchangeRate = null;

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

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $weight = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $completePackageWeight = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $externalSystemId = null;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $externalSystemLastSyncAt = null;

	/** @var ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem> */
	protected Collection|ArrayCollection $items;

	protected ?DocumentAddress $billingAddress = null;

	protected ?DocumentAddress $deliveryAddress = null;
	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $fakturoidNumber = null;
	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidIssuedAt = null;
	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $fakturoidId = null;
	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $fakturoidSubjectId = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidSentAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidPaidAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidReminderSentAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidAcceptedAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidCancelledAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidWebinvoiceSeenAt = null;

	#[Orm\Column(type: 'string', nullable: true)]
	protected ?string $fakturoidPublicToken = null;

	public function __construct(Project $project)
	{
		$this->project = $project;
		$this->items = new ArrayCollection();
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function setProject(Project $project): void
	{
		$this->project = $project;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	public function isPaid(): bool
	{
		return $this->paid;
	}

	public function setPaid(bool $paid): void
	{
		$this->paid = $paid;
	}

	public function getOrderCode(): ?string
	{
		return $this->orderCode;
	}

	public function setOrderCode(?string $orderCode): void
	{
		$this->orderCode = $orderCode;
	}

	public function isAddressesEqual(): bool
	{
		return $this->addressesEqual;
	}

	public function setAddressesEqual(bool $addressesEqual): void
	{
		$this->addressesEqual = $addressesEqual;
	}

	public function isValid(): bool
	{
		return $this->isValid;
	}

	public function setIsValid(bool $isValid): void
	{
		$this->isValid = $isValid;
	}

	public function getVarSymbol(): ?int
	{
		return $this->varSymbol;
	}

	public function setVarSymbol(?int $varSymbol): void
	{
		$this->varSymbol = $varSymbol;
	}

	public function getConstSymbol(): ?string
	{
		return $this->constSymbol;
	}

	public function setConstSymbol(?string $constSymbol): void
	{
		$this->constSymbol = $constSymbol;
	}

	public function getSpecSymbol(): ?int
	{
		return $this->specSymbol;
	}

	public function setSpecSymbol(?int $specSymbol): void
	{
		$this->specSymbol = $specSymbol;
	}

	public function getCreationTime(): DateTimeImmutable
	{
		return $this->creationTime;
	}

	public function setCreationTime(DateTimeImmutable $creationTime): void
	{
		$this->creationTime = $creationTime;
	}

	public function getChangeTime(): ?DateTimeImmutable
	{
		return $this->changeTime;
	}

	public function setChangeTime(?DateTimeImmutable $changeTime): void
	{
		$this->changeTime = $changeTime;
	}

	public function getDueDate(): ?DateTimeImmutable
	{
		return $this->dueDate;
	}

	public function setDueDate(?DateTimeImmutable $dueDate): void
	{
		$this->dueDate = $dueDate;
	}

	public function getBillingMethodId(): ?int
	{
		return $this->billingMethodId;
	}

	public function setBillingMethodId(?int $billingMethodId): void
	{
		$this->billingMethodId = $billingMethodId;
	}

	public function getBillingMethodName(): ?string
	{
		return $this->billingMethodName;
	}

	public function setBillingMethodName(?string $billingMethodName): void
	{
		$this->billingMethodName = $billingMethodName;
	}

	public function getVat(): ?float
	{
		return $this->vat;
	}

	public function setVat(?float $vat): void
	{
		$this->vat = $vat;
	}

	public function getVatRate(): ?float
	{
		return $this->vatRate;
	}

	public function setVatRate(?float $vatRate): void
	{
		$this->vatRate = $vatRate;
	}

	public function getToPay(): ?float
	{
		return $this->toPay;
	}

	public function setToPay(?float $toPay): void
	{
		$this->toPay = $toPay;
	}

	public function getCurrencyCode(): ?string
	{
		return $this->currencyCode;
	}

	public function setCurrencyCode(?string $currencyCode): void
	{
		$this->currencyCode = $currencyCode;
	}

	public function getWithVat(): ?float
	{
		return $this->withVat;
	}

	public function setWithVat(?float $withVat): void
	{
		$this->withVat = $withVat;
	}

	public function getWithoutVat(): ?float
	{
		return $this->withoutVat;
	}

	public function setWithoutVat(?float $withoutVat): void
	{
		$this->withoutVat = $withoutVat;
	}

	public function getExchangeRate(): ?float
	{
		return $this->exchangeRate;
	}

	public function setExchangeRate(?float $exchangeRate): void
	{
		$this->exchangeRate = $exchangeRate;
	}

	public function getEshopBankAccount(): ?string
	{
		return $this->eshopBankAccount;
	}

	public function setEshopBankAccount(?string $eshopBankAccount): void
	{
		$this->eshopBankAccount = $eshopBankAccount;
	}

	public function getEshopIban(): ?string
	{
		return $this->eshopIban;
	}

	public function setEshopIban(?string $eshopIban): void
	{
		$this->eshopIban = $eshopIban;
	}

	public function getEshopBic(): ?string
	{
		return $this->eshopBic;
	}

	public function setEshopBic(?string $eshopBic): void
	{
		$this->eshopBic = $eshopBic;
	}

	public function getEshopTaxMode(): ?string
	{
		return $this->eshopTaxMode;
	}

	public function setEshopTaxMode(?string $eshopTaxMode): void
	{
		$this->eshopTaxMode = $eshopTaxMode;
	}

	public function getEshopDocumentRemark(): ?string
	{
		return $this->eshopDocumentRemark;
	}

	public function setEshopDocumentRemark(?string $eshopDocumentRemark): void
	{
		$this->eshopDocumentRemark = $eshopDocumentRemark;
	}

	public function getVatPayer(): ?bool
	{
		return $this->vatPayer;
	}

	public function setVatPayer(?bool $vatPayer): void
	{
		$this->vatPayer = $vatPayer;
	}

	public function getWeight(): ?float
	{
		return $this->weight;
	}

	public function setWeight(?float $weight): void
	{
		$this->weight = $weight;
	}

	public function getCompletePackageWeight(): ?float
	{
		return $this->completePackageWeight;
	}

	public function setCompletePackageWeight(?float $completePackageWeight): void
	{
		$this->completePackageWeight = $completePackageWeight;
	}

	public function getExternalSystemId(): ?string
	{
		return $this->externalSystemId;
	}

	public function setExternalSystemId(?string $externalSystemId): void
	{
		$this->externalSystemId = $externalSystemId;
	}

	public function getExternalSystemLastSyncAt(): ?DateTimeImmutable
	{
		return $this->externalSystemLastSyncAt;
	}

	public function setExternalSystemLastSyncAt(?DateTimeImmutable $externalSystemLastSyncAt): void
	{
		$this->externalSystemLastSyncAt = $externalSystemLastSyncAt;
	}

	public function getBillingAddress(): ?DocumentAddress
	{
		return $this->billingAddress;
	}

	public function setBillingAddress(?DocumentAddress $billingAddress): void
	{
		$this->billingAddress = $billingAddress;
	}

	public function getDeliveryAddress(): ?DocumentAddress
	{
		return $this->deliveryAddress;
	}

	public function setDeliveryAddress(?DocumentAddress $deliveryAddress): void
	{
		$this->deliveryAddress = $deliveryAddress;
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

	/**
	 * @return ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem>
	 */
	public function getOnlyProductItems(): Collection|ArrayCollection
	{
		return $this->getItems()->filter(fn (DocumentItem $item) => !in_array($item->getItemType(), ['shipping', 'billing'], true));
	}

	/**
	 * @return ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem>
	 */
	public function getOnlyBillingAndShippingItems(): Collection|ArrayCollection
	{
		return $this->getItems()->filter(fn (DocumentItem $item) => in_array($item->getItemType(), ['shipping', 'billing'], true));
	}

	public function getCompanyId(): ?string
	{
		return $this->companyId;
	}

	public function setCompanyId(?string $companyId): void
	{
		$this->companyId = $companyId;
	}

	public function getVatId(): ?string
	{
		return $this->vatId;
	}

	public function setVatId(?string $vatId): void
	{
		$this->vatId = $vatId;
	}

	public function getTaxId(): ?string
	{
		return $this->taxId;
	}

	public function setTaxId(?string $taxId): void
	{
		$this->taxId = $taxId;
	}

	public function getFakturoidNumber(): ?string
	{
		return $this->fakturoidNumber;
	}

	public function setFakturoidNumber(?string $fakturoidNumber): void
	{
		$this->fakturoidNumber = $fakturoidNumber;
	}

	public function getFakturoidIssuedAt(): ?DateTimeImmutable
	{
		return $this->fakturoidIssuedAt;
	}

	public function setFakturoidIssuedAt(?DateTimeImmutable $fakturoidIssuedAt): void
	{
		$this->fakturoidIssuedAt = $fakturoidIssuedAt;
	}

	public function getFakturoidId(): ?int
	{
		return $this->fakturoidId;
	}

	public function setFakturoidId(?int $fakturoidId): void
	{
		$this->fakturoidId = $fakturoidId;
	}

	public function getFakturoidSubjectId(): ?int
	{
		return $this->fakturoidSubjectId;
	}

	public function setFakturoidSubjectId(?int $fakturoidSubjectId): void
	{
		$this->fakturoidSubjectId = $fakturoidSubjectId;
	}

	public function getFakturoidSentAt(): ?DateTimeImmutable
	{
		return $this->fakturoidSentAt;
	}

	public function setFakturoidSentAt(?DateTimeImmutable $fakturoidSentAt): void
	{
		$this->fakturoidSentAt = $fakturoidSentAt;
	}

	public function getFakturoidPaidAt(): ?DateTimeImmutable
	{
		return $this->fakturoidPaidAt;
	}

	public function setFakturoidPaidAt(?DateTimeImmutable $fakturoidPaidAt): void
	{
		$this->fakturoidPaidAt = $fakturoidPaidAt;
	}

	public function getFakturoidReminderSentAt(): ?DateTimeImmutable
	{
		return $this->fakturoidReminderSentAt;
	}

	public function setFakturoidReminderSentAt(?DateTimeImmutable $fakturoidReminderSentAt): void
	{
		$this->fakturoidReminderSentAt = $fakturoidReminderSentAt;
	}

	public function getFakturoidAcceptedAt(): ?DateTimeImmutable
	{
		return $this->fakturoidAcceptedAt;
	}

	public function setFakturoidAcceptedAt(?DateTimeImmutable $fakturoidAcceptedAt): void
	{
		$this->fakturoidAcceptedAt = $fakturoidAcceptedAt;
	}

	public function getFakturoidCancelledAt(): ?DateTimeImmutable
	{
		return $this->fakturoidCancelledAt;
	}

	public function setFakturoidCancelledAt(?DateTimeImmutable $fakturoidCancelledAt): void
	{
		$this->fakturoidCancelledAt = $fakturoidCancelledAt;
	}

	public function getFakturoidWebinvoiceSeenAt(): ?DateTimeImmutable
	{
		return $this->fakturoidWebinvoiceSeenAt;
	}

	public function setFakturoidWebinvoiceSeenAt(?DateTimeImmutable $fakturoidWebinvoiceSeenAt): void
	{
		$this->fakturoidWebinvoiceSeenAt = $fakturoidWebinvoiceSeenAt;
	}

	public function getFakturoidPublicToken(): ?string
	{
		return $this->fakturoidPublicToken;
	}

	public function setFakturoidPublicToken(?string $fakturoidPublicToken): void
	{
		$this->fakturoidPublicToken = $fakturoidPublicToken;
	}

	public function getShoptetCode(): ?string
	{
		return $this->shoptetCode;
	}

	public function setShoptetCode(?string $shoptetCode): void
	{
		$this->shoptetCode = $shoptetCode;
	}
}
