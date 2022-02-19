<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

abstract class Document
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $code;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public bool $isValid;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $orderCode = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public bool $addressesEqual = false;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'integer')]
	public int $varSymbol;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $constSymbol = null;


	#[Assert\Type(type: 'string')]
	public ?string $documentRemark = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'integer')]
	public ?int $specSymbol = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public DateTimeImmutable $creationTime;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?DateTimeImmutable $changeTime = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	#[Serializer\Type(name: 'DateTimeImmutable<\'Y\-m\-d\'>')]
	public ?DateTimeImmutable $dueDate = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: BillingMethod::class)]
	#[Serializer\Type(name: BillingMethod::class)]
	public ?BillingMethod $billingMethod = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: DocumentPrice::class)]
	#[Serializer\Type(name: DocumentPrice::class)]
	public ?DocumentPrice $price = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: Customer::class)]
	#[Serializer\Type(name: Customer::class)]
	public ?Customer $customer = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: DocumentEshop::class)]
	#[Serializer\Type(name: DocumentEshop::class)]
	public ?DocumentEshop $eshop = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: DocumentAddress::class)]
	#[Serializer\Type(name: DocumentAddress::class)]
	public ?DocumentAddress $billingAddress = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: DocumentAddress::class)]
	#[Serializer\Type(name: DocumentAddress::class)]
	public ?DocumentAddress $deliveryAddress = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: EetReceipt::class)]
	#[Serializer\Type(name: EetReceipt::class)]
	public ?EetReceipt $eetReceipt = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public ?bool $vatPayer = false;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public ?string $weight = null;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public ?string $completePackageWeight = null;

	/** @var DocumentItem[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, DocumentItem>')]
	public ?array $items = [];
}
