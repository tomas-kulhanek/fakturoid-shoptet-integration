<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints as Assert;

class ProductMainImage
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $name = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $seoName = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $cdnName = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'integer')]
	public ?int $priority = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $description = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: \DateTimeImmutable::class)]
	public ?\DateTimeImmutable $changeTim = null;
}
