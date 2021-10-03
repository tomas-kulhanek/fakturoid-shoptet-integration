<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\ProformaInvoice;

use App\DTO\Shoptet\Document;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ProformaInvoice extends Document
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'boolean')]
	public bool $paid = false;

	/** @var ProformaInvoiceItem[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, ProformaInvoiceItem>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\ProformaInvoice\ProformaInvoiceItem>')]
	public ?array $items = [];
}
