<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\ProformaInvoiceItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ProformaInvoiceItemRepository::class)]
#[ORM\Table(name: 'sf_proforma_invoice_item')]
#[ORM\HasLifecycleCallbacks]
class ProformaInvoiceItem extends DocumentItem
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: ProformaInvoice::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected ProformaInvoice|Document $document;
}
