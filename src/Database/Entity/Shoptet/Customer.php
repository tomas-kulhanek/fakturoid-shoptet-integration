<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\CustomerRepository;
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

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $guid;

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
	protected float $priceRatio;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?\DateTimeImmutable $birthDate = null;

	#[ORM\Column(type: 'boolean', nullable: true)]
	protected bool $disabledOrders = false;

	#[ORM\OneToOne(mappedBy: 'customer', targetEntity: CustomerBillingAddress::class)]
	protected ?CustomerBillingAddress $billingAddress = null;


	/** @var ArrayCollection<int, Order>|Collection<int, Order> */
	#[ORM\OneToMany(mappedBy: 'customer', targetEntity: Order::class)]
	protected Collection|ArrayCollection $orders;


	/** @var ArrayCollection<int, CustomerDeliveryAddress>|Collection<int, CustomerDeliveryAddress> */
	#[ORM\OneToMany(mappedBy: 'customer', targetEntity: CustomerDeliveryAddress::class)]
	protected Collection|ArrayCollection $deliveryAddress;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $adminUrl;


	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidCreatedAt = null;
	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidUpdatedAt = null;
	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $fakturoidId = null;

	public function __construct(Project $project, string $guid)
	{
		$this->guid = $guid;
		$this->project = $project;
		$this->orders = new ArrayCollection();
		$this->deliveryAddress = new ArrayCollection();
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getGuid(): string
	{
		return $this->guid;
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
		$this->deliveryAddress = $deliveryAddress;
	}

	public function getAdminUrl(): string
	{
		return $this->adminUrl;
	}

	public function setAdminUrl(string $adminUrl): void
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

	public function getFakturoidCreatedAt(): ?DateTimeImmutable
	{
		return $this->fakturoidCreatedAt;
	}

	public function setFakturoidCreatedAt(?DateTimeImmutable $fakturoidCreatedAt): void
	{
		$this->fakturoidCreatedAt = $fakturoidCreatedAt;
	}

	public function getFakturoidUpdatedAt(): ?DateTimeImmutable
	{
		return $this->fakturoidUpdatedAt;
	}

	public function setFakturoidUpdatedAt(?DateTimeImmutable $fakturoidUpdatedAt): void
	{
		$this->fakturoidUpdatedAt = $fakturoidUpdatedAt;
	}

	public function getFakturoidId(): ?int
	{
		return $this->fakturoidId;
	}

	public function setFakturoidId(?int $fakturoidId): void
	{
		$this->fakturoidId = $fakturoidId;
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
}
