<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ChangesResponse
{
	/** @var ChangeResponse[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, ChangeResponse>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\ChangeResponse>')]
	public ?array $changes = null;

	#[Assert\NotBlank()]
	#[Assert\Type(type: Paginator::class)]
	public Paginator $paginator;
}
