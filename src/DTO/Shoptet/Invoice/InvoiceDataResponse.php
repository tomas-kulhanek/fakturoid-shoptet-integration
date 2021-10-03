<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Invoice;

use Symfony\Component\Validator\Constraints as Assert;

class InvoiceDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: InvoiceResponse::class)]
	public InvoiceResponse $data;
}
