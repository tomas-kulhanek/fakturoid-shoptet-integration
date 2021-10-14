<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\PaymentMethod;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class PaymentMethodResponse
{
	/** @var PaymentMethod[] */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, PaymentMethod>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\PaymentMethod\PaymentMethod>')]
	public array $paymentMethods = [];

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $wholesaleActive = false;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $defaultRetailMethod = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $defaultWholesaleMethod = null;

}
