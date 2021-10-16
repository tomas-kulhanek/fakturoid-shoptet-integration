<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use Symfony\Component\Validator\Constraints as Assert;

class Currency
{
	#[Assert\NotBlank()]
	#[Assert\Type(type: 'string')]
	public string $code;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'string')]
	public string $title;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $isDefault = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $isDefaultAdmin = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public bool $isVisible = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'string')]
	public string $exchangeRate;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $priority;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $priceDecimalPlaces;

	#[Assert\NotBlank()]
	#[Assert\Choice(choices: ['none', 'up', 'down', 'math'])]
	public string $rounding = 'none';

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $minimalOrderValue = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: BankAccount::class)]
	public BankAccount $bankAccount;
}
