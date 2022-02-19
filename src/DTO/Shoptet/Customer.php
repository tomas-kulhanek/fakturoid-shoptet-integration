<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints as Assert;

class Customer
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $guid = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $phone = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $email = null;

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
}
