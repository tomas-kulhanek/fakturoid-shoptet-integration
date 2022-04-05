<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class DocumentItem
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $productGuid = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $code = null;

	#[Assert\NotBlank()]
	#[Assert\Choice(choices: ['product', 'bazar', 'service', 'shipping', 'billing', 'discount-coupon', 'volume-discount', 'gift', 'gift-certificate', 'generic-item', 'product-set'])]
	public string $itemType;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $name = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $variantName = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $brand = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $remark = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $weight = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $additionalField = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $amount = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $amountUnit = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $priceRatio = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: ItemPrice::class)]
	#[Serializer\Type(name: ItemPrice::class)]
	public ?ItemPrice $itemPrice = null;

	/** @var DocumentPrice[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'DocumentPrice[]')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\DocumentPrice>')]
	public ?array $displayPrices = [];

	public function getControlHash(): string
	{
		return sha1(
			implode(',', [
				$this->productGuid,
				$this->itemType,
				$this->name,
				$this->variantName,
				$this->amount,
			])
		);
	}
}
