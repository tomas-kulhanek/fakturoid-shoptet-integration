<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\SignatureKey;

use App\DTO\Shoptet\ErrorResponse;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class SignatureKeyResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: SignatureKeyDataResponse::class)]
	public ?SignatureKeyDataResponse $data = null;

	/** @var ErrorResponse[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, ErrorResponse>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\ErrorResponse>')]
	public ?array $errors = null;

	public function hasErrors(): bool
	{
		return $this->errors !== null && count($this->errors) > 0;
	}
}
