<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Customer;

use Symfony\Component\Validator\Constraints as Assert;

class CustomerAccount
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $fullName = null;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $email;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $phone = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public ?bool $mainAccount = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public ?bool $authorized = false;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'boolean')]
	public ?bool $emailVerified = false;
}
