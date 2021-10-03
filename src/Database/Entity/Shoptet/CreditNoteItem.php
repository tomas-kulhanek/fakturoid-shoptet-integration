<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\CreditNoteItemRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: CreditNoteItemRepository::class)]
#[ORM\Table(name: 'sf_credit_note_item')]
#[ORM\HasLifecycleCallbacks]
class CreditNoteItem extends DocumentItem
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: CreditNote::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected CreditNote|Document $document;
}
