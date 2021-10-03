<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\CreditNote;

use Symfony\Component\Validator\Constraints as Assert;

class CreditNoteDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: CreditNoteResponse::class)]
	public CreditNoteResponse $data;
}
