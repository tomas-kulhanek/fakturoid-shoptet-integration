<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\InvoiceBillingAddressRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: InvoiceBillingAddressRepository::class)]
#[ORM\Table(name: 'sf_invoice_billing_address')]
#[ORM\HasLifecycleCallbacks]
class InvoiceBillingAddress extends DocumentAddress
{
	use Attributes\TId;

	#[ORM\OneToOne(targetEntity: Invoice::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected Invoice|Document $document;
}
