<?php declare(strict_types=1);


namespace App\DTO\Shoptet\PaymentMethod;

use Symfony\Component\Validator\Constraints as Assert;

class PaymentMethod
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $guid;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $name;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $description = null;

	#[Assert\NotBlank]
	#[Assert\Type(type: PaymentType::class)]
	public ?PaymentType $paymentType = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $visible = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $priority;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $wholesale = false;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $logoUrl = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $eetEligible = false;

}
