<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\CustomerRepository;
use App\Mapping\CustomerMapping;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'sf_customer')]
#[ORM\HasLifecycleCallbacks]
class Customer
{
	use Attributes\TId;
	use Attributes\TGuid;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $shoptetGuid = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $controlHash;

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected DateTimeImmutable $creationTime;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $changeTime = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $companyId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $vatId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $clientCode = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $remark = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $email = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $phone = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected float $priceRatio = 0;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?\DateTimeImmutable $birthDate = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $disabledOrders = false;

	#[ORM\OneToOne(mappedBy: 'customer', targetEntity: CustomerBillingAddress::class)]
	protected ?CustomerBillingAddress $billingAddress = null;

	/** @var ArrayCollection<int, Order>|Collection<int, Order> */
	#[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
	protected Collection|ArrayCollection $orders;

	/** @var ArrayCollection<int, CustomerDeliveryAddress>|Collection<int, CustomerDeliveryAddress> */
	#[ORM\OneToMany(mappedBy: 'customer', targetEntity: CustomerDeliveryAddress::class)]
	protected Collection|ArrayCollection $deliveryAddress;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $adminUrl = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $endUser = false;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingCreatedAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $accountingUpdatedAt = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $accountingId = null;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $accountingForUpdate = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $accountingMapped = false;

	public function __construct(Project $project)
	{
		$this->project = $project;
		$this->orders = new ArrayCollection();
		$this->deliveryAddress = new ArrayCollection();
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getShoptetGuid(): ?string
	{
		return $this->shoptetGuid;
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

	public function getCompanyId(): ?string
	{
		return $this->companyId;
	}

	public function setCompanyId(?string $companyId): void
	{
		if ($this->companyId !== $companyId) {
			$this->accountingForUpdate = true;
		}
		$this->companyId = $companyId;
	}

	public function getVatId(): ?string
	{
		return $this->vatId;
	}

	public function setVatId(?string $vatId): void
	{
		if ($this->vatId !== $vatId) {
			$this->accountingForUpdate = true;
		}
		$this->vatId = $vatId;
	}

	public function getClientCode(): ?string
	{
		return $this->clientCode;
	}

	public function setClientCode(?string $clientCode): void
	{
		$this->clientCode = $clientCode;
	}

	public function getRemark(): ?string
	{
		return $this->remark;
	}

	public function setRemark(?string $remark): void
	{
		$this->remark = $remark;
	}

	public function getPriceRatio(): float
	{
		return $this->priceRatio;
	}

	public function setPriceRatio(float $priceRatio): void
	{
		$this->priceRatio = $priceRatio;
	}

	public function getBirthDate(): ?DateTimeImmutable
	{
		return $this->birthDate;
	}

	public function setBirthDate(?DateTimeImmutable $birthDate): void
	{
		if ($this->birthDate !== $birthDate) {
			$this->accountingForUpdate = true;
		}
		$this->birthDate = $birthDate;
	}

	public function isDisabledOrders(): bool
	{
		return $this->disabledOrders;
	}

	public function setDisabledOrders(bool $disabledOrders): void
	{
		$this->disabledOrders = $disabledOrders;
	}

	public function getBillingAddress(): ?CustomerBillingAddress
	{
		return $this->billingAddress;
	}

	public function setBillingAddress(CustomerBillingAddress $billingAddress): void
	{
		$billingAddress->setCustomer($this);

		if ($this->billingAddress !== $billingAddress) {
			$this->accountingForUpdate = true;
		}
		$this->billingAddress = $billingAddress;
	}

	/**
	 * @return ArrayCollection<int, CustomerDeliveryAddress>|Collection<int, CustomerDeliveryAddress>
	 */
	public function getDeliveryAddress(): ArrayCollection|Collection
	{
		return $this->deliveryAddress;
	}

	/**
	 * @param ArrayCollection<int, CustomerDeliveryAddress>|Collection<int, CustomerDeliveryAddress> $deliveryAddress
	 */
	public function setDeliveryAddress(ArrayCollection|Collection $deliveryAddress): void
	{
		if ($this->deliveryAddress !== $deliveryAddress) {
			$this->accountingForUpdate = true;
		}
		$this->deliveryAddress = $deliveryAddress;
	}

	public function getAdminUrl(): ?string
	{
		return $this->adminUrl;
	}

	public function setAdminUrl(?string $adminUrl): void
	{
		$this->adminUrl = $adminUrl;
	}

	/**
	 * @return ArrayCollection<int, Order>|Collection<int, Order>
	 */
	public function getOrders(): ArrayCollection|Collection
	{
		return $this->orders;
	}

	/**
	 * @param ArrayCollection<int, Order>|Collection<int, Order> $orders
	 */
	public function setOrders(ArrayCollection|Collection $orders): void
	{
		$this->orders = $orders;
	}

	public function getAccountingCreatedAt(): ?DateTimeImmutable
	{
		return $this->accountingCreatedAt;
	}

	public function setAccountingCreatedAt(?DateTimeImmutable $accountingCreatedAt): void
	{
		$this->accountingCreatedAt = $accountingCreatedAt;
	}

	public function getAccountingUpdatedAt(): ?DateTimeImmutable
	{
		return $this->accountingUpdatedAt;
	}

	public function setAccountingUpdatedAt(?DateTimeImmutable $accountingUpdatedAt): void
	{
		$this->accountingUpdatedAt = $accountingUpdatedAt;
	}

	public function getAccountingId(): ?int
	{
		return $this->accountingId;
	}

	public function setAccountingId(?int $accountingId): void
	{
		$this->accountingId = $accountingId;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setEmail(?string $email): void
	{
		if ($this->email !== $email) {
			$this->accountingForUpdate = true;
		}
		$this->email = $email;
	}

	public function getPhone(): ?string
	{
		return $this->phone;
	}

	public function setPhone(?string $phone): void
	{
		if ($this->phone !== $phone) {
			$this->accountingForUpdate = true;
		}
		$this->phone = $phone;
	}

	public function setShoptetGuid(?string $shoptetGuid): void
	{
		$this->shoptetGuid = $shoptetGuid;
	}

	/** @internal */
	#[ORM\PreFlush]
	public function computeControlHash(): void
	{
		$this->controlHash = CustomerMapping::getControlHashFromCustomer($this);
	}

	public function isEndUser(): bool
	{
		return $this->endUser;
	}

	public function setEndUser(bool $endUser): void
	{
		$this->endUser = $endUser;
	}

	public function isAccountingForUpdate(): bool
	{
		return $this->accountingForUpdate;
	}

	public function setAccountingForUpdate(bool $accountingForUpdate): void
	{
		$this->accountingForUpdate = $accountingForUpdate;
	}

	public function isAccountingMapped(): bool
	{
		return $this->accountingMapped;
	}

	public function setAccountingMapped(bool $accountingMapped): void
	{
		$this->accountingMapped = $accountingMapped;
	}
}
