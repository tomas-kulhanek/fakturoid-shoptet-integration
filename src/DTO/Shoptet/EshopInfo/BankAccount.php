<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use Symfony\Component\Validator\Constraints as Assert;

class BankAccount
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $accountNumber = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $iban = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $bic = null;
}
