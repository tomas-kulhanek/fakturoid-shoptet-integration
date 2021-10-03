<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Invoice;

use App\DTO\Shoptet\Document;
use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class Invoice extends Document
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $proformaInvoiceCode = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	#[Serializer\Type(name: 'DateTimeImmutable<\'Y\-m\-d\'>')]
	public ?DateTimeImmutable $taxDate = null;

	/** @var InvoiceItem[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, InvoiceItem>')]
	#[Serializer\Type(name: 'array<int, App\DTO\Shoptet\Invoice\InvoiceItem>')]
	public ?array $items = [];
}
