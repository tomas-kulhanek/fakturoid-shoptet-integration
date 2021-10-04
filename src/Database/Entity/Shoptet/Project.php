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

	#[ORM\Column(type: 'text', nullable: false)]
	protected string $accessToken;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $tokenType;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $scope;
	#[ORM\Column(type: 'integer', nullable: false, options: ['default' => self::STATE_NOT_INITIALIZED])]
	protected int $state = self::STATE_NOT_INITIALIZED;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $eshopId;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $eshopUrl;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $contactEmail;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'owner_id', nullable: false, onDelete: 'CASCADE')]
	protected User $owner;

	/** @var ArrayCollection<int, User>|Collection<int, User> */
	#[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projects')]
	#[ORM\JoinTable(name: 'sf_users_projects')]
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

	public function __construct()
	{
		$this->receivedWebhooks = new ArrayCollection();
		$this->registeredWebhooks = new ArrayCollection();
		$this->proformaInvoices = new ArrayCollection();
		$this->invoices = new ArrayCollection();
		$this->creditNotes = new ArrayCollection();
		$this->users = new ArrayCollection();
		$this->orderStatuses = new ArrayCollection();
	}

	public function setOwner(User $owner): void
	{
		$this->owner = $owner;
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

	public function setEshopUrl(string $eshopUrl): void
	{
		$this->eshopUrl = $eshopUrl;
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
}
