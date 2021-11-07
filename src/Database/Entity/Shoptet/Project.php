<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\User;
use App\Database\Repository\Shoptet\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Nette\Http\Url;
use Ramsey\Uuid\Uuid;

#[Orm\Entity(repositoryClass: ProjectRepository::class)]
#[ORM\Table(name: 'sf_projects')]
#[ORM\HasLifecycleCallbacks]
class Project
{
	use Attributes\TId;
	use Attributes\TGuid;
	use Attributes\TCreatedAt;

	public const STATE_SUSPENDED = -1;
	public const STATE_ACTIVE = 1;
	public const STATE_NOT_INITIALIZED = 0;

	public const STATES = [
		self::STATE_SUSPENDED,
		self::STATE_NOT_INITIALIZED,
		self::STATE_NOT_INITIALIZED,
	];

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected \DateTimeImmutable $lastCustomerSyncAt;
	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected \DateTimeImmutable $lastOrderSyncAt;
	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected \DateTimeImmutable $lastInvoiceSyncAt;
	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected \DateTimeImmutable $lastProformaSyncAt;

	#[ORM\Column(type: 'text', nullable: false)]
	protected string $accessToken;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $tokenType;

	#[ORM\Column(type: 'text', nullable: true)]
	protected ?string $signingKey = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $name;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $scope;
	#[ORM\Column(type: 'integer', nullable: false, options: ['default' => self::STATE_NOT_INITIALIZED])]
	protected int $state = self::STATE_NOT_INITIALIZED;

	#[ORM\Column(type: 'integer', unique: true, nullable: false)]
	protected int $eshopId;

	#[ORM\Column(type: 'string', unique: true, nullable: false)]
	protected string $eshopUrl;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $contactEmail;

	#[ORM\Column(type: 'string', unique: true, nullable: false)]
	protected string $identifier;

	/** @var ArrayCollection<int, User>|Collection<int, User> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: User::class)]
	protected Collection|ArrayCollection $users;

	/** @var ArrayCollection<int, ReceivedWebhook>|Collection<int, ReceivedWebhook> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: ReceivedWebhook::class)]
	protected Collection|ArrayCollection $receivedWebhooks;

	/** @var ArrayCollection<int, RegisteredWebhook>|Collection<int, RegisteredWebhook> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: RegisteredWebhook::class)]
	protected Collection|ArrayCollection $registeredWebhooks;

	/** @var ArrayCollection<int, ProformaInvoice>|Collection<int, ProformaInvoice> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: ProformaInvoice::class)]
	protected Collection|ArrayCollection $proformaInvoices;

	/** @var ArrayCollection<int, Invoice>|Collection<int, Invoice> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: Invoice::class)]
	protected Collection|ArrayCollection $invoices;

	/** @var ArrayCollection<int, CreditNote>|Collection<int, CreditNote> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: CreditNote::class)]
	protected Collection|ArrayCollection $creditNotes;

	#[ORM\OneToOne(mappedBy: 'project', targetEntity: ProjectSetting::class)]
	protected ?ProjectSetting $settings = null;

	/** @var ArrayCollection<int, OrderStatus>|Collection<int, OrderStatus> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: OrderStatus::class)]
	protected Collection|ArrayCollection $orderStatuses;

	/** @var ArrayCollection<int, Currency>|Collection<int, Currency> */
	#[ORM\OneToMany(mappedBy: 'project', targetEntity: Currency::class)]
	protected Collection|ArrayCollection $currencies;

	public function __construct()
	{
		$this->receivedWebhooks = new ArrayCollection();
		$this->registeredWebhooks = new ArrayCollection();
		$this->proformaInvoices = new ArrayCollection();
		$this->invoices = new ArrayCollection();
		$this->creditNotes = new ArrayCollection();
		$this->users = new ArrayCollection();
		$this->orderStatuses = new ArrayCollection();
		$this->currencies = new ArrayCollection();
		$this->lastCustomerSyncAt = (new \DateTimeImmutable())->modify('-30 days');
		$this->lastInvoiceSyncAt = (new \DateTimeImmutable())->modify('-30 days');
		$this->lastProformaSyncAt = (new \DateTimeImmutable())->modify('-30 days');
		$this->lastOrderSyncAt = (new \DateTimeImmutable())->modify('-30 days');
	}

	public function addUser(User $user): void
	{
		if (!$this->users->contains($user)) {
			$this->users->add($user);
		}
	}

	public function getAccessToken(): string
	{
		return $this->accessToken;
	}

	public function setAccessToken(string $accessToken): void
	{
		$this->accessToken = $accessToken;
	}

	public function getTokenType(): string
	{
		return $this->tokenType;
	}

	public function setTokenType(string $tokenType): void
	{
		$this->tokenType = $tokenType;
	}

	public function getScope(): string
	{
		return $this->scope;
	}

	public function setScope(string $scope): void
	{
		$this->scope = $scope;
	}

	public function getEshopId(): int
	{
		return $this->eshopId;
	}

	public function setEshopId(int $eshopId): void
	{
		$this->eshopId = $eshopId;
	}

	public function getEshopUrl(): string
	{
		return $this->eshopUrl;
	}

	public function getEshopHost(): string
	{
		$url = new Url($this->getEshopUrl());
		return $url->getHost();
	}

	public function setEshopUrl(string $eshopUrl): void
	{
		$this->eshopUrl = $eshopUrl;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getContactEmail(): string
	{
		return $this->contactEmail;
	}

	public function setContactEmail(string $contactEmail): void
	{
		$this->contactEmail = $contactEmail;
	}

	public function addReceivedWebhook(ReceivedWebhook $receivedWebhook): void
	{
		if (!$this->receivedWebhooks->contains($receivedWebhook)) {
			$this->receivedWebhooks->add($receivedWebhook);
		}
	}

	/**
	 * @return ArrayCollection<int, User>|Collection<int, User>
	 */
	public function getUsers(): ArrayCollection|Collection
	{
		return $this->users;
	}

	public function isActive(): bool
	{
		return $this->state === self::STATE_ACTIVE;
	}

	public function isSuspended(): bool
	{
		return $this->state === self::STATE_SUSPENDED;
	}

	public function initialize(): void
	{
		$this->state = self::STATE_ACTIVE;
	}

	public function suspend(): void
	{
		$this->state = self::STATE_SUSPENDED;
	}

	public function getSettings(): ?ProjectSetting
	{
		return $this->settings;
	}

	/**
	 * @return ArrayCollection<int, RegisteredWebhook>|Collection<int, RegisteredWebhook>
	 */
	public function getRegisteredWebhooks(): ArrayCollection|Collection
	{
		return $this->registeredWebhooks;
	}

	/**
	 * @return ArrayCollection<int, OrderStatus>|Collection<int, OrderStatus>
	 */
	public function getOrderStatuses(): ArrayCollection|Collection
	{
		return $this->orderStatuses;
	}

	/**
	 * @return ArrayCollection<int, Currency>|Collection<int, Currency>
	 */
	public function getCurrencies(): ArrayCollection|Collection
	{
		return $this->currencies;
	}

	public function getLastCustomerSyncAt(): \DateTimeImmutable
	{
		return $this->lastCustomerSyncAt;
	}

	public function setLastCustomerSyncAt(\DateTimeImmutable $lastCustomerSyncAt): void
	{
		$this->lastCustomerSyncAt = $lastCustomerSyncAt;
	}

	public function getLastOrderSyncAt(): \DateTimeImmutable
	{
		return $this->lastOrderSyncAt;
	}

	public function setLastOrderSyncAt(\DateTimeImmutable $lastOrderSyncAt): void
	{
		$this->lastOrderSyncAt = $lastOrderSyncAt;
	}

	public function getLastInvoiceSyncAt(): \DateTimeImmutable
	{
		return $this->lastInvoiceSyncAt;
	}

	public function setLastInvoiceSyncAt(\DateTimeImmutable $lastInvoiceSyncAt): void
	{
		$this->lastInvoiceSyncAt = $lastInvoiceSyncAt;
	}

	public function getLastProformaSyncAt(): \DateTimeImmutable
	{
		return $this->lastProformaSyncAt;
	}

	public function setLastProformaSyncAt(\DateTimeImmutable $lastProformaSyncAt): void
	{
		$this->lastProformaSyncAt = $lastProformaSyncAt;
	}

	/** @internal */
	#[ORM\PrePersist]
	public function setIdentifier(): void
	{
		$this->identifier = substr(sha1(Uuid::uuid4()->toString()), 0, 200);
	}

	public function getIdentifier(): string
	{
		return $this->identifier;
	}

	public function getSigningKey(): string
	{
		return $this->signingKey;
	}

	public function setSigningKey(string $signingKey): void
	{
		$this->signingKey = $signingKey;
	}
}
