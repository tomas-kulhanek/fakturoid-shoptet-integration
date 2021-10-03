<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes\TCreatedAt;
use App\Database\Entity\Attributes\TGuid;
use App\Database\Entity\Attributes\TId;
use App\Database\Entity\Attributes\TUpdatedAt;
use App\Database\Entity\Shoptet\Project;
use App\Database\Repository\UserRepository;
use App\Exception\Logic\InvalidArgumentException;
use App\Security\Identity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'core_user')]
#[ORM\HasLifecycleCallbacks]
class User extends AbstractEntity
{
	public const ROLE_ADMIN = 'admin';
	public const ROLE_USER = 'user';

	public const STATE_FRESH = 1;
	public const STATE_ACTIVATED = 2;
	public const STATE_BLOCKED = 3;

	public const STATES = [self::STATE_FRESH, self::STATE_BLOCKED, self::STATE_ACTIVATED];

	use TId;
	use TCreatedAt;
	use TUpdatedAt;
	use TGuid;

	#[ORM\Column(type: 'string')]
	private string $firstName;

	#[ORM\Column(type: 'string')]
	private string $lastName;

	#[ORM\Column(type: 'string', unique: true)]
	private string $email;

	#[ORM\Column(type: 'integer', length: 10)]
	private int $state = self::STATE_FRESH;

	#[ORM\Column(type: 'string')]
	private string $password;

	#[ORM\Column(type: 'string')]
	private string $role = self::ROLE_USER;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	private ?\DateTimeImmutable $lastLoggedAt = null;

	/** @var ArrayCollection<int, Project>|Collection<int, Project> */
	#[ORM\OneToMany(mappedBy: 'user', targetEntity: Project::class)]
	protected Collection|ArrayCollection $project;

	public function __construct(string $firstName, string $lastName, string $email, string $passwordHash)
	{
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->email = $email;
		$this->password = $passwordHash;
	}

	public function changeLoggedAt(): void
	{
		$this->lastLoggedAt = new \DateTimeImmutable();
	}

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getLastLoggedAt(): ?\DateTimeImmutable
	{
		return $this->lastLoggedAt;
	}

	public function getRole(): string
	{
		return $this->role;
	}

	public function setRole(string $role): void
	{
		$this->role = $role;
	}

	public function getPasswordHash(): string
	{
		return $this->password;
	}

	public function changePasswordHash(string $password): void
	{
		$this->password = $password;
	}

	public function block(): void
	{
		$this->state = self::STATE_BLOCKED;
	}

	public function activate(): void
	{
		$this->state = self::STATE_ACTIVATED;
	}

	public function isActivated(): bool
	{
		return $this->state === self::STATE_ACTIVATED;
	}

	public function isBlocked(): bool
	{
		return $this->state === self::STATE_BLOCKED;
	}

	public function getFirstName(): string
	{
		return $this->firstName;
	}

	public function getLastName(): string
	{
		return $this->lastName;
	}


	public function getFullname(): string
	{
		return $this->getFirstName() . ' ' . $this->getLastName();
	}

	public function rename(string $firstName, string $lastName): void
	{
		$this->firstName = $firstName;
		$this->lastName = $lastName;
	}

	public function getState(): int
	{
		return $this->state;
	}

	public function setState(int $state): void
	{
		if (!in_array($state, self::STATES, true)) {
			throw new InvalidArgumentException(sprintf('Unsupported state %s', $state));
		}

		$this->state = $state;
	}

	public function getGravatar(): string
	{
		return 'https://www.gravatar.com/avatar/' . md5($this->email);
	}

	/**
	 * @param array<string, mixed> $userData
	 * @return Identity
	 */
	public function toIdentity(array $userData): Identity
	{
		return new Identity($this->getId(), [$this->role], array_merge([
			'user' => $this,
			'email' => $this->email,
			'firstName' => $this->getFirstName(),
			'lastName' => $this->getLastName(),
			'state' => $this->state,
			'gravatar' => $this->getGravatar(),
		], $userData));
	}
}
