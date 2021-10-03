<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\CreditNoteDeliveryAddressRepository;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: CreditNoteDeliveryAddressRepository::class)]
#[ORM\Table(name: 'sf_credit_note_delivery_address')]
#[ORM\HasLifecycleCallbacks]
class CreditNoteDeliveryAddress extends DocumentAddress
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: CreditNote::class)]
	protected CreditNote|Document $document;
}
