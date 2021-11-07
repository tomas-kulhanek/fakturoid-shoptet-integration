<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\DocumentAddress;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoiceBillingAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceItem;
use App\Database\Entity\Shoptet\Project;

class ProformaInvoiceSaver extends DocumentSaver
{
	public function save(Project $project, \App\DTO\Shoptet\ProformaInvoice\ProformaInvoice $proformaInvoice): ProformaInvoice
	{
		/** @var ProformaInvoice $document */
		$document = $this->pairByCodeAndProject($project, $proformaInvoice->code);
		if ($proformaInvoice->changeTime instanceof \DateTimeImmutable) {
			if ($document->getChangeTime() instanceof \DateTimeImmutable && $document->getChangeTime() >= $proformaInvoice->changeTime) {
				return $document;
			}
		}
		$this->fillBasicData($document, $proformaInvoice);
		$this->fillBillingAddress($document, $proformaInvoice);
		$this->fillDeliveryAddress($document, $proformaInvoice);
		$this->processItems($document, $proformaInvoice);
		$this->fillCustomerData($document, $proformaInvoice);
		$document->setDeletedAt(null);

		$document->setPaid($proformaInvoice->paid);
		$this->entityManager->flush();

		return $document;
	}


	protected function getDocumentClassName(): string
	{
		return ProformaInvoice::class;
	}

	protected function getItemEntity(): DocumentItem
	{
		return new ProformaInvoiceItem();
	}

	protected function getBillingAddressEntity(): DocumentAddress
	{
		return new ProformaInvoiceBillingAddress();
	}

	protected function getDeliveryAddressEntity(): DocumentAddress
	{
		return new ProformaInvoiceDeliveryAddress();
	}
}
