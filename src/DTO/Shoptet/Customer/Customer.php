<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Customer;

use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Customer
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $guid;

	#[Assert\NotBlank()]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public DateTimeImmutable $creationTime;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?DateTimeImmutable $changeTime = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $companyId = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $vatId = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $clientCode = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $remark = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'float')]
	#[Serializer\Type(name: 'float')]
	public float $priceRatio;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?\DateTimeImmutable $birthDate = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $disabledOrders = false;


	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: CustomerAddress::class)]
	public CustomerAddress $billingAddress;

	/** @var CustomerAddress[] */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, CustomerAddress>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Customer\CustomerAddress>')]
	public array $deliveryAddress = [];

	/** @var CustomerAccount[] */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, CustomerAccount>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Customer\CustomerAccount>')]
	public array $accounts = [];

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $adminUrl;
}
