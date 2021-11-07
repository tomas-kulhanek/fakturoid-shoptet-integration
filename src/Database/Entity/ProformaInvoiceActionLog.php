<?php

namespace App\Database\Entity;

use App\Database\Entity\Shoptet\ProformaInvoice;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity()]
class ProformaInvoiceActionLog extends ActionLog
{
	#[ORM\ManyToOne(targetEntity: ProformaInvoice::class)]
	#[ORM\JoinColumn(name: 'proforma_invoice_id', nullable: true, onDelete: 'CASCADE')]
	protected ProformaInvoice $proformaInvoice;

	public function getActionLogType(): string
	{
		return 'proforma-invoice';
	}

	public function getDocument(): ProformaInvoice
	{
		return $this->proformaInvoice;
	}

	public function setDocument(ProformaInvoice $proformaInvoice): void
	{
		$this->proformaInvoice = $proformaInvoice;
	}
}
