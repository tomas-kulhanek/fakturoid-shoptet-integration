<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\ReceivedWebhookRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ReceivedWebhookRepository::class)]
#[ORM\Table(name: 'sf_received_webhooks')]
#[ORM\HasLifecycleCallbacks]
class ReceivedWebhook
{
	use Attributes\TId;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $eshopId;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $event;

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected DateTimeImmutable $eventCreated;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $eventInstance;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected DateTimeImmutable $lastReceived;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $receiveCount = 1;

	public function __construct(Project $project, int $eshopId, string $event, string $eventInstance, DateTimeImmutable $eventCreated)
	{
		$this->eshopId = $eshopId;
		$this->event = $event;
		$this->eventCreated = $eventCreated;
		$this->eventInstance = $eventInstance;
		$this->project = $project;
		$this->lastReceived = $eventCreated;
	}

	public function getEshopId(): int
	{
		return $this->eshopId;
	}

	public function getEvent(): string
	{
		return $this->event;
	}

	public function getEventCreated(): DateTimeImmutable
	{
		return $this->eventCreated;
	}

	public function getEventInstance(): string
	{
		return $this->eventInstance;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function setLastReceived(DateTimeImmutable $lastReceived): void
	{
		$this->lastReceived = $lastReceived;
	}

	public function incrementCounter(): void
	{
		$this->receiveCount += 1;
	}
}
