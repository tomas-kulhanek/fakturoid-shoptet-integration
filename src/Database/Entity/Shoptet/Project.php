<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Entity\User;
use App\Database\Repository\Shoptet\ProjectRepository;
use DateTimeImmutable;
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
	use Attributes\TUpdatedAt;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $accessToken;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $tokenType;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $scope;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $eshopId;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $eshopUrl;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $contactEmail;

	#[ORM\Column(type: 'string', nullable: false)]
	protected bool $revoked = false;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $revokedAt = null;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'user_id', nullable: false, onDelete: 'CASCADE')]
	protected User $user;

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

	public function __construct(User $user)
	{
		$this->user = $user;
		$this->receivedWebhooks = new ArrayCollection();
		$this->registeredWebhooks = new ArrayCollection();
		$this->proformaInvoices = new ArrayCollection();
		$this->invoices = new ArrayCollection();
		$this->creditNotes = new ArrayCollection();
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

	public function isRevoked(): bool
	{
		return $this->revoked;
	}

	public function setRevoked(bool $revoked): void
	{
		$this->revoked = $revoked;
	}

	public function getRevokedAt(): ?DateTimeImmutable
	{
		return $this->revokedAt;
	}

	public function setRevokedAt(?DateTimeImmutable $revokedAt): void
	{
		$this->revokedAt = $revokedAt;
	}

	public function addReceivedWebhook(ReceivedWebhook $receivedWebhook): void
	{
		if (!$this->receivedWebhooks->contains($receivedWebhook)) {
			$this->receivedWebhooks->add($receivedWebhook);
		}
	}
}
