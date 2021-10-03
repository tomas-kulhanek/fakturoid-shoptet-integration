<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\ProformaInvoice;

use Symfony\Component\Validator\Constraints as Assert;

class ProformaInvoiceDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: ProformaInvoiceResponse::class)]
	public ProformaInvoiceResponse $data;
}
