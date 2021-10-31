<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Invoice;

use App\DTO\Shoptet\ErrorResponse;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class InvoiceDataResponse
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: InvoiceResponse::class)]
	public ?InvoiceResponse $data = null;

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
