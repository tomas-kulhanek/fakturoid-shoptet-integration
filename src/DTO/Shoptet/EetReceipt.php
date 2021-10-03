<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class EetReceipt
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $uuid = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public bool $firstSent = false;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $vatId = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?DateTimeImmutable $revenueDate = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'float')]
	public ?float $totalRevenue = null;
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'float')]
	public ?float $vatBase1 = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'float')]
	public ?float $vat1 = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?float $vatBase2 = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'float')]
	public ?float $vat2 = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'float')]
	public ?float $vatBase3 = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'float')]
	public ?float $vat3 = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'float')]
	public ?float $nonTaxableBase = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $exchangeRate = null;
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $pkp = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $bkp = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $fik = null;

	#[Assert\NotBlank(allowNull: false)]
	#[Assert\Type(type: 'integer')]
	public ?int $mode = 0;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	#[Assert\Choice(choices: ['Sandbox', 'Production'])]
	public ?string $eetMode = 'Sandbox';

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?DateTimeImmutable $sent = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $cashDeskId = null;
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Choice(choices: ['order', 'invoice'])]
	public ?string $documentType = 'order';

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public bool $isActive = false;
}
