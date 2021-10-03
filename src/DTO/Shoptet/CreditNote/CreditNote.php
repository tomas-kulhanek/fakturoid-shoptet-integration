<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\CreditNote;

use App\DTO\Shoptet\Document;
use DateTimeImmutable;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreditNote extends Document
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'string')]
	public string $invoiceCode;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	#[Serializer\Type(name: 'DateTimeImmutable<\'Y\-m\-d\'>')]
	public ?DateTimeImmutable $taxDate = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Choice(choices: [null, 'load', 'unload'])]
	public ?string $stockAmountChangeType = null;

	/** @var CreditNoteItem[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, CreditNoteItem>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\CreditNote\CreditNoteItem>')]
	public ?array $items = [];
}
