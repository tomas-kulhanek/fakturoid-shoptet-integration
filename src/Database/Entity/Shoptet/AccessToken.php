<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\AccessTokenRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: AccessTokenRepository::class)]
#[ORM\Table(name: 'sf_access_token')]
#[ORM\HasLifecycleCallbacks]
class AccessToken
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'string', nullable: false)]
	public string $accessToken;

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	public \DateTimeImmutable $expiresIn;

	#[ORM\Column(type: 'boolean', nullable: false)]
	public bool $leased = false;

	public function __construct(
		Project            $project,
		string             $accessToken,
		\DateTimeImmutable $expiresIn
	) {
		$this->project = $project;
		$this->accessToken = $accessToken;
		$this->expiresIn = $expiresIn;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getAccessToken(): string
	{
		return $this->accessToken;
	}

	public function getExpiresIn(): DateTimeImmutable
	{
		return $this->expiresIn;
	}

	public function isLeased(): bool
	{
		return $this->leased;
	}

	public function setLeased(bool $leased): void
	{
		$this->leased = $leased;
	}
}
