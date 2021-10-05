<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\CreditNoteBillingAddressRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: CreditNoteBillingAddressRepository::class)]
#[ORM\Table(name: 'sf_credit_note_billing_address')]
#[ORM\HasLifecycleCallbacks]
class CreditNoteBillingAddress extends DocumentAddress
{
	use Attributes\TId;

	#[ORM\OneToOne(targetEntity: CreditNote::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected CreditNote|Document $document;
}
