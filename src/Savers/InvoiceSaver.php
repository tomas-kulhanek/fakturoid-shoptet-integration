<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\DocumentAddress;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceBillingAddress;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Project;

class InvoiceSaver extends DocumentSaver
{
	public function save(Project $project, \App\DTO\Shoptet\Invoice\Invoice $invoice): Invoice
	{
		$document = $this->pairByCodeAndProject($project, $invoice->code);
		if ($invoice->changeTime instanceof \DateTimeImmutable) {
			if ($document->getChangeTime() instanceof \DateTimeImmutable && $document->getChangeTime() >= $invoice->changeTime) {
				/** @var Invoice $document */
				return $document;
			}
		}

		$this->fillBasicData($document, $invoice);
		$this->fillBillingAddress($document, $invoice);
		$this->fillDeliveryAddress($document, $invoice);
		$this->processItems($document, $invoice);

		/** @var Invoice $document */
		$document->setTaxDate($invoice->taxDate);
		$document->setProformaInvoiceCode($invoice->proformaInvoiceCode);
		$document->setDocumentRemark($invoice->documentRemark);

		$document->setPaid(true);
		$this->entityManager->flush();

		return $document;
	}


	protected function getDocumentClassName(): string
	{
		return Invoice::class;
	}

	protected function getItemEntity(): DocumentItem
	{
		return new InvoiceItem();
	}

	protected function getBillingAddressEntity(): DocumentAddress
	{
		return new InvoiceBillingAddress();
	}

	protected function getDeliveryAddressEntity(): DocumentAddress
	{
		return new InvoiceDeliveryAddress();
	}
}
