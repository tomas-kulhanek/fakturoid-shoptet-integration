<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\ProformaInvoiceBillingAddressRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ProformaInvoiceBillingAddressRepository::class)]
#[ORM\Table(name: 'sf_proforma_invoice_billing_address')]
#[ORM\HasLifecycleCallbacks]
class ProformaInvoiceBillingAddress extends DocumentAddress
{
	use Attributes\TId;

	#[ORM\OneToOne(targetEntity: ProformaInvoice::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected ProformaInvoice|Document $document;
}
