<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $guid;
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $code;
	#[Assert\NotBlank]
	#[Assert\Type(type: 'datetime')]
	public \DateTimeImmutable $changeTime;
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $changeType;
}
