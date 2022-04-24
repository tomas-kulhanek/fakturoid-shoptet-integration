<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Mapping\BillingMethodMapper;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\MappedSuperclass]
abstract class Document implements DocumentInterface
{
	use Attributes\TGuid;

	private ?int $id = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	#[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $accountingNumberLineId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $email = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $phone = null;

	#[ORM\ManyToOne(targetEntity: Order::class)]
	#[ORM\JoinColumn(name: 'order_id', nullable: true, onDelete: 'SET NULL')]
	protected ?Order $order = null;

	#[ORM\ManyToOne(targetEntity: Currency::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'currency_id', nullable: false, onDelete: 'RESTRICT')]
	protected Currency $currency;

	#[ORM\Column(name:'`code`', type: 'string', nullable: false)]
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

	#[ORM\Column(type: 'string', length: 15, nullable: true)]
	protected ?string $varSymbol = null;

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

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $deletedAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $dueDate = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $issueDate = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	public ?int $billingMethodId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $billingMethod = null;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $vat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $vatRate = null;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $toPay = null;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $currencyCode = null;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $withVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	public ?float $withoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $mainWithVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $mainWithoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $mainToPay = null;

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

	/** @var ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem> */
	protected Collection|ArrayCollection $items;

	#[ORM\ManyToOne(targetEntity: Customer::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'customer_id', nullable: true, onDelete: 'SET NULL')]
	protected ?Customer $customer = null;

	protected ?DocumentAddress $billingAddress = null;

	protected ?DocumentAddress $deliveryAddress = null;
	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingNumber = null;
	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingIssuedAt = null;
	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $accountingId = null;
	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $accountingSubjectId = null;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	protected bool $accountingError = false;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $accountingLastErrors = null;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingUpdatedAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingSentAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingPaidAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingAcceptedAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingCancelledAt = null;

	#[Orm\Column(type: 'string', nullable: true)]
	protected ?string $accountingPublicHtmlUrl = null;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	protected bool $accountingPaid = false;

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

	public function getVarSymbol(): ?string
	{
		return $this->varSymbol;
	}

	public function setVarSymbol(?string $varSymbol): void
	{
		$this->varSymbol = $varSymbol;
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
		return $this->getItems()->filter(fn (DocumentItem $item) => !$item->getDeletedAt() instanceof DateTimeImmutable && !in_array($item->getItemType(), ['shipping', 'billing'], true));
	}

	/**
	 * @return ArrayCollection<int, DocumentItem>|Collection<int, DocumentItem>
	 */
	public function getOnlyBillingAndShippingItems(): Collection|ArrayCollection
	{
		return $this->getItems()->filter(fn (DocumentItem $item) => !$item->getDeletedAt() instanceof DateTimeImmutable && in_array($item->getItemType(), ['shipping', 'billing'], true));
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

	public function getAccountingNumber(): ?string
	{
		return $this->accountingNumber;
	}

	public function setAccountingNumber(?string $accountingNumber): void
	{
		$this->accountingNumber = $accountingNumber;
	}

	public function getAccountingIssuedAt(): ?DateTimeImmutable
	{
		return $this->accountingIssuedAt;
	}

	public function setAccountingIssuedAt(?DateTimeImmutable $accountingIssuedAt): void
	{
		$this->accountingIssuedAt = $accountingIssuedAt;
	}

	public function getAccountingId(): ?int
	{
		return $this->accountingId;
	}

	public function setAccountingId(?int $accountingId): void
	{
		$this->accountingId = $accountingId;
	}

	public function getAccountingSubjectId(): ?int
	{
		return $this->accountingSubjectId;
	}

	public function setAccountingSubjectId(?int $accountingSubjectId): void
	{
		$this->accountingSubjectId = $accountingSubjectId;
	}

	public function getAccountingSentAt(): ?DateTimeImmutable
	{
		return $this->accountingSentAt;
	}

	public function setAccountingSentAt(?DateTimeImmutable $accountingSentAt): void
	{
		$this->accountingSentAt = $accountingSentAt;
	}

	public function getAccountingPaidAt(): ?DateTimeImmutable
	{
		return $this->accountingPaidAt;
	}

	public function setAccountingPaidAt(?DateTimeImmutable $accountingPaidAt): void
	{
		$this->accountingPaidAt = $accountingPaidAt;
	}

	public function getAccountingAcceptedAt(): ?DateTimeImmutable
	{
		return $this->accountingAcceptedAt;
	}

	public function setAccountingAcceptedAt(?DateTimeImmutable $accountingAcceptedAt): void
	{
		$this->accountingAcceptedAt = $accountingAcceptedAt;
	}

	public function getAccountingCancelledAt(): ?DateTimeImmutable
	{
		return $this->accountingCancelledAt;
	}

	public function setAccountingCancelledAt(?DateTimeImmutable $accountingCancelledAt): void
	{
		$this->accountingCancelledAt = $accountingCancelledAt;
	}

	public function getAccountingPublicHtmlUrl(): ?string
	{
		return $this->accountingPublicHtmlUrl;
	}

	public function setAccountingPublicHtmlUrl(?string $accountingPublicToken): void
	{
		$this->accountingPublicHtmlUrl = $accountingPublicToken;
	}

	public function getShoptetCode(): ?string
	{
		return $this->shoptetCode;
	}

	public function setShoptetCode(?string $shoptetCode): void
	{
		$this->shoptetCode = $shoptetCode;
	}

	public function getMainWithVat(): ?float
	{
		return $this->mainWithVat;
	}

	public function setMainWithVat(?float $mainWithVat): void
	{
		$this->mainWithVat = $mainWithVat;
	}

	public function getMainWithoutVat(): ?float
	{
		return $this->mainWithoutVat;
	}

	public function setMainWithoutVat(?float $mainWithoutVat): void
	{
		$this->mainWithoutVat = $mainWithoutVat;
	}

	public function getMainToPay(): ?float
	{
		return $this->mainToPay;
	}

	public function setMainToPay(?float $mainToPay): void
	{
		$this->mainToPay = $mainToPay;
	}

	public function changeGuid(UuidInterface $uuid): void
	{
		$this->guid = $uuid;
	}

	public function getBillingMethod(): ?string
	{
		return $this->billingMethod;
	}

	public function setBillingMethod(?string $billingMethod): void
	{
		if ($billingMethod !== null && !in_array($billingMethod, BillingMethodMapper::BILLING_METHODS, true)) {
			throw new \LogicException();
		}
		$this->billingMethod = $billingMethod;
	}

	public function getCurrency(): Currency
	{
		return $this->currency;
	}

	public function setCurrency(Currency $currency): void
	{
		$this->currency = $currency;
	}

	public function getOrder(): ?Order
	{
		return $this->order;
	}

	public function setOrder(?Order $order): void
	{
		$this->order = $order;
	}

	public function getDeletedAt(): ?DateTimeImmutable
	{
		return $this->deletedAt;
	}

	public function isDeleted(): bool
	{
		return $this->getDeletedAt() instanceof DateTimeImmutable;
	}

	public function setDeletedAt(?DateTimeImmutable $deletedAt): void
	{
		$this->deletedAt = $deletedAt;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer): void
	{
		$this->customer = $customer;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(?string $email): void
	{
		$this->email = $email;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): void
	{
		$this->phone = $phone;
	}

	public function getIssueDate(): ?DateTimeImmutable
	{
		return $this->issueDate;
	}

	public function setIssueDate(?DateTimeImmutable $issueDate): void
	{
		$this->issueDate = $issueDate;
	}

	public function getAccountingUpdatedAt(): ?DateTimeImmutable
	{
		return $this->accountingUpdatedAt;
	}

	public function setAccountingUpdatedAt(?DateTimeImmutable $accountingUpdatedAt): void
	{
		$this->accountingUpdatedAt = $accountingUpdatedAt;
	}

	public function isAccountingError(): bool
	{
		return $this->accountingError;
	}

	public function setAccountingError(bool $accountingError): void
	{
		$this->accountingError = $accountingError;
	}

	public function setAccountingLastError(?string $accountingLastErrors): void
	{
		$this->accountingLastErrors = $accountingLastErrors;
	}

	public function getAccountingLastError(): ?string
	{
		return $this->accountingLastErrors;
	}


	public function isAccountingPaid(): bool
	{
		return $this->accountingPaid;
	}

	public function setAccountingPaid(bool $accountingPaid): void
	{
		$this->accountingPaid = $accountingPaid;
	}

	public function getAccountingNumberLineId(): ?int
	{
		return $this->accountingNumberLineId;
	}

	public function setAccountingNumberLineId(?int $accountingNumberLineId): void
	{
		$this->accountingNumberLineId = $accountingNumberLineId;
	}
}
