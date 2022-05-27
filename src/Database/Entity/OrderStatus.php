<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes;
use App\Database\Entity\Shoptet\Project;

use App\Database\Repository\OrderStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: OrderStatusRepository::class)]
#[ORM\Table(name: 'system_order_status')]
#[ORM\HasLifecycleCallbacks]
class OrderStatus
{
	use Attributes\TId;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $shoptetId = null;

	#[ORM\Column(name: '`rank`', type: 'integer', nullable: true)]
	protected ?int $rank = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $name;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $markAsPaid = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $setAfterPaidIsReceived = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $createInvoice = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $createProforma = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	protected bool $isDefault = false;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	protected Project $project;

	#[ORM\Column(name: '`type`', type: 'string', nullable: false)]
	protected string $type = 'primary';

	public function __construct(Project $project)
	{
		$this->project = $project;
	}

	public function isDefault(): bool
	{
		return $this->isDefault;
	}

	public function setIsDefault(bool $isDefault): void
	{
		$this->isDefault = $isDefault;
	}

	public function getShoptetId(): ?int
	{
		return $this->shoptetId;
	}

	public function setShoptetId(int $shoptetId): void
	{
		$this->shoptetId = $shoptetId;
	}

	public function getRank(): ?int
	{
		return $this->rank;
	}

	public function setRank(int $rank): void
	{
		$this->rank = $rank;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function isMarkAsPaid(): bool
	{
		return $this->markAsPaid;
	}

	public function setMarkAsPaid(bool $markAsPaid): void
	{
		$this->markAsPaid = $markAsPaid;
	}

	public function isCreateInvoice(): bool
	{
		return $this->createInvoice;
	}

	public function setCreateInvoice(bool $createInvoice): void
	{
		$this->createInvoice = $createInvoice;
	}

	public function isCreateProforma(): bool
	{
		return $this->createProforma;
	}

	public function setCreateProforma(bool $createProforma): void
	{
		$this->createProforma = $createProforma;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function isSetAfterPaidIsReceived(): bool
	{
		return $this->setAfterPaidIsReceived;
	}

	public function setSetAfterPaidIsReceived(bool $setAfterPaidIsReceived): void
	{
		$this->setAfterPaidIsReceived = $setAfterPaidIsReceived;
	}
}
