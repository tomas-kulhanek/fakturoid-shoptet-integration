<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\ProformaInvoice;

use App\DTO\Shoptet\ErrorResponse;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ProformaInvoiceDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: ProformaInvoiceResponse::class)]
	public ProformaInvoiceResponse $data;

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
