<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\CreditNote;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreditNoteResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: CreditNote::class)]
	#[Serializer\Type(name: CreditNote::class)]
	public CreditNote $creditNote;
}
