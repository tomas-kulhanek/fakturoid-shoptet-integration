<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class ConfirmInstallation
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $access_token;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public ?\DateTimeImmutable $expires_in = null;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $token_type;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $scope;

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $eshopId;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $eshopUrl;

	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $contactEmail;
}
