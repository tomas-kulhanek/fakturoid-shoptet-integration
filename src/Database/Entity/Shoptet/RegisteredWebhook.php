<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use DateTimeImmutable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'sf_registered_webhooks')]
#[ORM\HasLifecycleCallbacks]
class RegisteredWebhook
{
	#[ORM\Column(type: 'integer', nullable: false, options: ['unsigned' => true])]
	#[ORM\Id]
	protected int $id;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $event;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $url;

	#[ORM\Column(type: 'datetime_immutable', nullable: false)]
	protected DateTimeImmutable $createdAt;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $updatedAt;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	public function __construct(
		int $id,
		string $event,
		string $url,
		DateTimeImmutable $createdAt,
		Project $project
	) {
		$this->id = $id;
		$this->event = $event;
		$this->url = $url;
		$this->createdAt = $createdAt;
		$this->project = $project;
	}
}
