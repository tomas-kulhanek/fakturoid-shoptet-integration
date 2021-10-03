<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use App\DTO\Shoptet\BillingMethod;
use App\DTO\Shoptet\DocumentAddress;
use App\DTO\Shoptet\DocumentPrice;
use App\DTO\Shoptet\OrderStatus;
use App\DTO\Shoptet\PaymentMethod;
use App\DTO\Shoptet\ShippingMethod;
use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Order
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $code;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $externalCode = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public DateTimeImmutable $creationTime;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?DateTimeImmutable $changeTime = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $email = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $phone = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?DateTimeImmutable $birthDate = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $clientCode = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $companyId = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $vatId = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $taxId = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public ?bool $vatPayer = false;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $customerGuid = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $addressesEqual = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $cashDeskOrder = false;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'integer')]
	public ?int $stockId = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public ?bool $paid = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'string')]
	public string $adminUrl;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $onlinePaymentLink = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'string')]
	public string $language;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $referer = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: BillingMethod::class)]
	public ?BillingMethod $billingMethod = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: DocumentAddress::class)]
	public ?DocumentAddress $billingAddress = null;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: DocumentAddress::class)]
	public ?DocumentAddress $deliveryAddress = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: OrderStatus::class)]
	public OrderStatus $status;

	#[Assert\NotBlank(allowNull: null)]
	#[Assert\Type(type: DocumentPrice::class)]
	public ?DocumentPrice $price = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: PaymentMethod::class)]
	#[Serializer\Type(name: PaymentMethod::class)]
	public PaymentMethod $paymentMethod;

	#[Assert\NotBlank()]
	#[Assert\Type(type: ShippingMethod::class)]
	#[Serializer\Type(name: ShippingMethod::class)]
	public ShippingMethod $shipping;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $clientIPAddress = null;

	/** @var OrderPaymentMethods[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, OrderPaymentMethods>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Order\OrderPaymentMethods>')]
	public ?array $paymentMethods = [];

	/** @var OrderShippingMethods[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, OrderShippingMethods>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Order\OrderShippingMethods>')]
	public ?array $shippings = [];

	/** @var OrderItem[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, OrderItem>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Order\OrderItem>')]
	public ?array $items = [];


	/** @var OrderNote[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, OrderNote>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Order\OrderNote>')]
	public ?array $notes = [];


	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: ShippingDetail::class)]
	#[Serializer\Type(name: ShippingDetail::class)]
	public ?ShippingDetail $shippingDetails = null;
}
