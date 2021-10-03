<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\ProformaInvoice;

use Symfony\Component\Validator\Constraints as Assert;

class ProformaInvoiceResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: ProformaInvoice::class)]
	public ProformaInvoice $proformaInvoice;
}
