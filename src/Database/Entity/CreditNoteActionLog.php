<?php

namespace App\Database\Entity;

use App\Database\Entity\Shoptet\CreditNote;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class CreditNoteActionLog extends ActionLog
{
	#[ORM\ManyToOne(targetEntity: CreditNote::class)]
	#[ORM\JoinColumn(name: 'proforma_invoice_id', nullable: true, onDelete: 'CASCADE')]
	protected CreditNote $creditNote;

	public function getActionLogType(): string
	{
		return 'proforma-invoice';
	}

	public function getDocument(): CreditNote
	{
		return $this->creditNote;
	}

	public function setDocument(CreditNote $creditNote): void
	{
		$this->creditNote = $creditNote;
	}
}
