<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use Symfony\Component\Validator\Constraints as Assert;

class OrderNote
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $customerRemark = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $eshopRemark = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $trackingNumber = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $trackingUrl = null;

	/** @var OrderNoteAdditionalFields[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, OrderNoteAdditionalFields>')]
	public ?array $additionalFields = [];
}
