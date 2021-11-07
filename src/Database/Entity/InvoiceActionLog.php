<?php

namespace App\Database\Entity;

use App\Database\Entity\Shoptet\Invoice;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class InvoiceActionLog extends ActionLog
{
	#[ORM\ManyToOne(targetEntity: Invoice::class)]
	#[ORM\JoinColumn(name: 'invoice_id', nullable: true, onDelete: 'CASCADE')]
	protected Invoice $invoice;

	public function getActionLogType(): string
	{
		return 'invoice';
	}

	public function getDocument(): Invoice
	{
		return $this->invoice;
	}

	public function setDocument(Invoice $invoice): void
	{
		$this->invoice = $invoice;
	}
}
