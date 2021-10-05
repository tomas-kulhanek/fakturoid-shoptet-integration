<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;


use App\Database\Repository\Shoptet\OrderPaymentMethodsRepository;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: OrderPaymentMethodsRepository::class)]
#[ORM\Table(name: 'sf_order_payment_method')]
#[ORM\HasLifecycleCallbacks]
class OrderPaymentMethods
{
	use Attributes\TId;

	#[ORM\OneToOne(targetEntity: Order::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected Order $document;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $guid = null;

	#[ORM\Column(type: 'string', nullable: true)]
	public ?string $name = null;

	#[ORM\Column(type: 'integer', nullable: false)]
	public int $itemId;

	public function setDocument(Order $document): void
	{
		$this->document = $document;
	}

	public function setGuid(?string $guid): void
	{
		$this->guid = $guid;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function setItemId(int $itemId): void
	{
		$this->itemId = $itemId;
	}

	public function getDocument(): Order
	{
		return $this->document;
	}

	public function getGuid(): ?string
	{
		return $this->guid;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getItemId(): int
	{
		return $this->itemId;
	}
}
