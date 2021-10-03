<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Invoice;

use App\DTO\Shoptet\DocumentItem;
use App\DTO\Shoptet\ItemRecyclingFee;
use Symfony\Component\Validator\Constraints as Assert;

class InvoiceItem extends DocumentItem
{
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: ItemRecyclingFee::class)]
	public ?ItemRecyclingFee $recyclingFee = null;
}
