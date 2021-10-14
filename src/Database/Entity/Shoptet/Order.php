<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Entity\OrderStatus;
use App\Database\Repository\Shoptet\OrderRepository;

use App\Mapping\BillingMethodMapper;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'sf_order')]
#[ORM\HasLifecycleCallbacks]
class Order
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Project::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\ManyToOne(targetEntity: Customer::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'customer_id', nullable: true, onDelete: 'SET NULL')]
	protected ?Customer $customer = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $code;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $shoptetCode = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $externalCode = null;

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected DateTimeImmutable $creationTime;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $changeTime = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $email = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $phone = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $clientCode = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $companyId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $vatId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $taxId = null;

	#[ORM\Column(type: 'boolean', nullable: true)]
	protected ?bool $vatPayer = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $addressesEqual = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $cashDeskOrder = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected ?bool $paid = false;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $adminUrl;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $language;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $billingMethodId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $billingMethod = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: OrderBillingAddress::class)]
	protected ?OrderBillingAddress $billingAddress = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: OrderDeliveryAddress::class)]
	protected ?OrderDeliveryAddress $deliveryAddress = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceVatRate = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceToPay = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $priceCurrencyCode = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceWithVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceWithoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $mainPriceWithVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $mainPriceWithoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceExchangeRate = null;

	/** @var ArrayCollection<int, OrderPaymentMethods>|Collection<int, OrderPaymentMethods> */
	#[ORM\OneToMany(mappedBy: 'document', targetEntity: OrderPaymentMethods::class)]
	protected Collection|ArrayCollection $paymentMethods;

	/** @var ArrayCollection<int, OrderShippingMethods>|Collection<int, OrderShippingMethods> */
	#[ORM\OneToMany(mappedBy: 'document', targetEntity: OrderShippingMethods::class)]
	protected Collection|ArrayCollection $shippings;

	/** @var ArrayCollection<int, OrderItem>|Collection<int, OrderItem> */
	#[ORM\OneToMany(mappedBy: 'document', targetEntity: OrderItem::class)]
	protected Collection|ArrayCollection $items;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: OrderShippingDetail::class)]
	protected ?OrderShippingDetail $shippingDetail = null;

	#[ORM\ManyToOne(targetEntity: OrderStatus::class)]
	#[ORM\JoinColumn(name: 'status_id', nullable: false, onDelete: 'RESTRICT')]
	protected OrderStatus $status;

	/** @var ArrayCollection<int, Invoice>|Collection<int, Invoice> */
	#[ORM\OneToMany(mappedBy: 'order', targetEntity: Invoice::class)]
	protected Collection|ArrayCollection $invoices;

	/** @var ArrayCollection<int, ProformaInvoice>|Collection<int, ProformaInvoice> */
	#[ORM\OneToMany(mappedBy: 'order', targetEntity: ProformaInvoice::class)]
	protected Collection|ArrayCollection $proformaInvoices;
	/** @var ArrayCollection<int, CreditNote>|Collection<int, CreditNote> */
	#[ORM\OneToMany(mappedBy: 'order', targetEntity: CreditNote::class)]
	protected Collection|ArrayCollection $creditNotes;

	public function __construct(Project $project)
	{
		$this->project = $project;
		$this->paymentMethods = new ArrayCollection();
		$this->shippings = new ArrayCollection();
		$this->items = new ArrayCollection();
		$this->invoices = new ArrayCollection();
		$this->proformaInvoices = new ArrayCollection();
		$this->creditNotes = new ArrayCollection();
	}

	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	public function setExternalCode(?string $externalCode): void
	{
		$this->externalCode = $externalCode;
	}

	public function setCreationTime(DateTimeImmutable $creationTime): void
	{
		$this->creationTime = $creationTime;
	}

	public function setChangeTime(?DateTimeImmutable $changeTime): void
	{
		$this->changeTime = $changeTime;
	}

	public function setEmail(?string $email): void
	{
		$this->email = $email;
	}

	public function setPhone(?string $phone): void
	{
		$this->phone = $phone;
	}

	public function setClientCode(?string $clientCode): void
	{
		$this->clientCode = $clientCode;
	}

	public function setCompanyId(?string $companyId): void
	{
		$this->companyId = $companyId;
	}

	public function setVatId(?string $vatId): void
	{
		$this->vatId = $vatId;
	}

	public function setTaxId(?string $taxId): void
	{
		$this->taxId = $taxId;
	}

	public function setVatPayer(?bool $vatPayer): void
	{
		$this->vatPayer = $vatPayer;
	}

	public function setAddressesEqual(bool $addressesEqual): void
	{
		$this->addressesEqual = $addressesEqual;
	}

	public function setCashDeskOrder(bool $cashDeskOrder): void
	{
		$this->cashDeskOrder = $cashDeskOrder;
	}

	public function setPaid(?bool $paid): void
	{
		$this->paid = $paid;
	}

	public function setAdminUrl(string $adminUrl): void
	{
		$this->adminUrl = $adminUrl;
	}

	public function setLanguage(string $language): void
	{
		$this->language = $language;
	}

	public function setBillingMethodId(?int $billingMethodId): void
	{
		$this->billingMethodId = $billingMethodId;
	}

	public function setBillingAddress(?OrderBillingAddress $billingAddress): void
	{
		$this->billingAddress = $billingAddress;
	}

	public function setDeliveryAddress(?OrderDeliveryAddress $deliveryAddress): void
	{
		$this->deliveryAddress = $deliveryAddress;
	}

	public function setPriceVat(?float $priceVat): void
	{
		$this->priceVat = $priceVat;
	}

	public function setPriceVatRate(?float $priceVatRate): void
	{
		$this->priceVatRate = $priceVatRate;
	}

	public function setPriceToPay(?float $priceToPay): void
	{
		$this->priceToPay = $priceToPay;
	}

	public function setPriceCurrencyCode(?string $priceCurrencyCode): void
	{
		$this->priceCurrencyCode = $priceCurrencyCode;
	}

	public function setPriceWithVat(?float $priceWithVat): void
	{
		$this->priceWithVat = $priceWithVat;
	}

	public function setPriceWithoutVat(?float $priceWithoutVat): void
	{
		$this->priceWithoutVat = $priceWithoutVat;
	}

	public function setPriceExchangeRate(?float $priceExchangeRate): void
	{
		$this->priceExchangeRate = $priceExchangeRate;
	}

	/**
	 * @param ArrayCollection<int, OrderPaymentMethods>|Collection<int, OrderPaymentMethods> $paymentMethods
	 */
	public function setPaymentMethods(ArrayCollection|Collection $paymentMethods): void
	{
		$this->paymentMethods = $paymentMethods;
	}

	/**
	 * @param ArrayCollection<int, OrderShippingMethods>|Collection<int, OrderShippingMethods> $shippings
	 */
	public function setShippings(ArrayCollection|Collection $shippings): void
	{
		$this->shippings = $shippings;
	}

	/**
	 * @param ArrayCollection<int, OrderItem>|Collection<int, OrderItem> $items
	 */
	public function setItems(ArrayCollection|Collection $items): void
	{
		$this->items = $items;
	}

	public function setShippingDetail(?OrderShippingDetail $shippingDetail): void
	{
		$this->shippingDetail = $shippingDetail;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function getExternalCode(): ?string
	{
		return $this->externalCode;
	}

	public function getCreationTime(): DateTimeImmutable
	{
		return $this->creationTime;
	}

	public function getChangeTime(): ?DateTimeImmutable
	{
		return $this->changeTime;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function getClientCode(): ?string
	{
		return $this->clientCode;
	}

	public function getCompanyId(): ?string
	{
		return $this->companyId;
	}

	public function getVatId(): ?string
	{
		return $this->vatId;
	}

	public function getTaxId(): ?string
	{
		return $this->taxId;
	}

	public function getVatPayer(): ?bool
	{
		return $this->vatPayer;
	}

	public function isAddressesEqual(): bool
	{
		return $this->addressesEqual;
	}

	public function isCashDeskOrder(): bool
	{
		return $this->cashDeskOrder;
	}

	public function getPaid(): ?bool
	{
		return $this->paid;
	}

	public function getAdminUrl(): string
	{
		return $this->adminUrl;
	}

	public function getLanguage(): string
	{
		return $this->language;
	}

	public function getBillingMethodId(): ?int
	{
		return $this->billingMethodId;
	}

	public function getBillingAddress(): ?OrderBillingAddress
	{
		return $this->billingAddress;
	}

	public function getDeliveryAddress(): ?OrderDeliveryAddress
	{
		return $this->deliveryAddress;
	}

	public function getPriceVat(): ?float
	{
		return $this->priceVat;
	}

	public function getPriceVatRate(): ?float
	{
		return $this->priceVatRate;
	}

	public function getPriceToPay(): ?float
	{
		return $this->priceToPay;
	}

	public function getPriceCurrencyCode(): ?string
	{
		return $this->priceCurrencyCode;
	}

	public function getPriceWithVat(): ?float
	{
		return $this->priceWithVat;
	}

	public function getPriceWithoutVat(): ?float
	{
		return $this->priceWithoutVat;
	}

	public function getPriceExchangeRate(): ?float
	{
		return $this->priceExchangeRate;
	}

	public function getStatus(): OrderStatus
	{
		return $this->status;
	}

	public function setStatus(OrderStatus $status): void
	{
		$this->status = $status;
	}

	/**
	 * @return ArrayCollection<int, OrderPaymentMethods>|Collection<int, OrderPaymentMethods>
	 */
	public function getPaymentMethods(): ArrayCollection|Collection
	{
		return $this->paymentMethods;
	}

	/**
	 * @return ArrayCollection<int, OrderShippingMethods>|Collection<int, OrderShippingMethods>
	 */
	public function getShippings(): ArrayCollection|Collection
	{
		return $this->shippings;
	}

	/**
	 * @return ArrayCollection<int, OrderItem>|Collection<int, OrderItem>
	 */
	public function getOnlyProductItems(): Collection|ArrayCollection
	{
		return $this->getItems()->filter(fn (OrderItem $item) => !in_array($item->getItemType(), ['shipping', 'billing'], true));
	}

	/**
	 * @return ArrayCollection<int, OrderItem>|Collection<int, OrderItem>
	 */
	public function getOnlyBillingAndShippingItems(): Collection|ArrayCollection
	{
		return $this->getItems()->filter(fn (OrderItem $item) => in_array($item->getItemType(), ['shipping', 'billing'], true));
	}

	/**
	 * @return ArrayCollection<int, OrderItem>|Collection<int, OrderItem>
	 */
	public function getItems(): ArrayCollection|Collection
	{
		return $this->items;
	}

	public function getShippingDetail(): ?OrderShippingDetail
	{
		return $this->shippingDetail;
	}

	public function addItem(OrderItem $documentItem): void
	{
		if (!$this->getItems()->contains($documentItem)) {
			$this->getItems()->add($documentItem);
		}
	}

	public function addPaymentMethod(OrderPaymentMethods $documentItem): void
	{
		if (!$this->getPaymentMethods()->contains($documentItem)) {
			$this->getPaymentMethods()->add($documentItem);
		}
	}

	public function addShippingMethod(OrderShippingMethods $documentItem): void
	{
		if (!$this->getShippings()->contains($documentItem)) {
			$this->getShippings()->add($documentItem);
		}
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	/**
	 * @return ArrayCollection<int, Invoice>|Collection<int, Invoice>
	 */
	public function getInvoices(): ArrayCollection|Collection
	{
		return $this->invoices;
	}

	/**
	 * @return ArrayCollection<int, ProformaInvoice>|Collection<int, ProformaInvoice>
	 */
	public function getProformaInvoices(): ArrayCollection|Collection
	{
		return $this->proformaInvoices;
	}

	/**
	 * @return ArrayCollection<int, CreditNote>|Collection<int, CreditNote>
	 */
	public function getCreditNotes(): ArrayCollection|Collection
	{
		return $this->creditNotes;
	}

	public function getShoptetCode(): ?string
	{
		return $this->shoptetCode;
	}

	public function setShoptetCode(?string $shoptetCode): void
	{
		$this->shoptetCode = $shoptetCode;
	}

	public function getCustomer(): ?Customer
	{
		return $this->customer;
	}

	public function setCustomer(?Customer $customer): void
	{
		$this->customer = $customer;
	}

	public function getMainPriceWithVat(): ?float
	{
		return $this->mainPriceWithVat;
	}

	public function setMainPriceWithVat(?float $mainPriceWithVat): void
	{
		$this->mainPriceWithVat = $mainPriceWithVat;
	}

	public function getMainPriceWithoutVat(): ?float
	{
		return $this->mainPriceWithoutVat;
	}

	public function setMainPriceWithoutVat(?float $mainPriceWithoutVat): void
	{
		$this->mainPriceWithoutVat = $mainPriceWithoutVat;
	}

	public function containsNonAccountedItems(): bool
	{
		return !$this->getItems()->filter(fn (OrderItem $orderItem) => !$orderItem->isAccounted())->isEmpty();
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
}
