<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\InvoiceRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\Table(name: 'sf_invoice_item')]
#[ORM\HasLifecycleCallbacks]
class InvoiceItem extends DocumentItem
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Invoice::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected Invoice|Document $document;
}
