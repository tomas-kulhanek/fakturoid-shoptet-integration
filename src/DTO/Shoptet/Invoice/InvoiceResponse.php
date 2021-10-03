<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Invoice;

use Symfony\Component\Validator\Constraints as Assert;

class InvoiceResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: Invoice::class)]
	public Invoice $invoice;
}
