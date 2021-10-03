<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\ProformaInvoiceDeliveryAddressRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ProformaInvoiceDeliveryAddressRepository::class)]
#[ORM\Table(name: 'sf_proforma_invoice_delivery_address')]
#[ORM\HasLifecycleCallbacks]
class ProformaInvoiceDeliveryAddress extends DocumentAddress
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: ProformaInvoice::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected ProformaInvoice|Document $document;
}
