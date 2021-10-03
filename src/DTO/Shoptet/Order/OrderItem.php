<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use App\DTO\Shoptet\DocumentItem;
use App\DTO\Shoptet\ItemPrice;
use App\DTO\Shoptet\ItemRecyclingFee;
use App\DTO\Shoptet\OrderStatus;
use App\DTO\Shoptet\ProductMainImage;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class OrderItem extends DocumentItem
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $supplierName = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $amountCompleted = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: ItemPrice::class)]
	#[Serializer\Type(name: ItemPrice::class)]
	public ?ItemPrice $buyPrice = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: ItemRecyclingFee::class)]
	#[Serializer\Type(name: ItemRecyclingFee::class)]
	public ?ItemRecyclingFee $recyclingFee = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: OrderStatus::class)]
	#[Serializer\Type(name: OrderStatus::class)]
	public ?OrderStatus $status = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: ProductMainImage::class)]
	#[Serializer\Type(name: ProductMainImage::class)]
	public ?ProductMainImage $mainImage = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $stockLocation = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $itemId;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $warrantyDescription = null;
}
