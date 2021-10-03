<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\CreditNote;
use App\Database\Entity\Shoptet\CreditNoteBillingAddress;
use App\Database\Entity\Shoptet\CreditNoteDeliveryAddress;
use App\Database\Entity\Shoptet\CreditNoteItem;
use App\Database\Entity\Shoptet\DocumentAddress;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Project;

class CreditNoteSaver extends DocumentSaver
{
	public function save(Project $project, \App\DTO\Shoptet\CreditNote\CreditNote $creditNote): CreditNote
	{
		$document = $this->pairByCodeAndProject($project, $creditNote->code);
		if ($creditNote->changeTime instanceof \DateTimeImmutable) {
			if ($document->getChangeTime() instanceof \DateTimeImmutable && $document->getChangeTime() >= $creditNote->changeTime) {
				/** @var CreditNote $document */
				return $document;
			}
		}

		$this->fillBasicData($document, $creditNote);
		$this->fillBillingAddress($document, $creditNote);
		$this->fillDeliveryAddress($document, $creditNote);
		$this->processItems($document, $creditNote);

		/** @var CreditNote $document */
		$document->setStockAmountChangeType($creditNote->stockAmountChangeType);
		$document->setInvoiceCode($creditNote->invoiceCode);
		$document->setTaxDate($creditNote->taxDate);
		$document->setDocumentRemark($creditNote->documentRemark);
		//$document->setPaid($creditNote->paid);
		$this->entityManager->flush();

		return $document;
	}


	protected function getDocumentClassName(): string
	{
		return CreditNote::class;
	}

	protected function getItemEntity(): DocumentItem
	{
		return new CreditNoteItem();
	}

	protected function getBillingAddressEntity(): DocumentAddress
	{
		return new CreditNoteBillingAddress();
	}

	protected function getDeliveryAddressEntity(): DocumentAddress
	{
		return new CreditNoteDeliveryAddress();
	}
}
