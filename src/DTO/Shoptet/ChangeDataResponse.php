<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: ChangesResponse::class)]
	#[Serializer\Type(name: ChangesResponse::class)]
	public ?ChangesResponse $data = null;

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
