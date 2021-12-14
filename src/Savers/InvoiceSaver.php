<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\DocumentAddress;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceBillingAddress;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceEET;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\EetReceipt;
use App\Manager\CurrencyManager;
use App\Manager\CustomerManager;
use App\Manager\InvoiceManager;
use App\Manager\OrderManager;
use App\Manager\ProformaInvoiceManager;
use App\Mapping\BillingMethodMapper;
use App\Mapping\CustomerMapping;

class InvoiceSaver extends DocumentSaver
{
	public function __construct(
		EntityManager                  $entityManager,
		BillingMethodMapper            $billingMethodMapper,
		CurrencyManager                $currencyManager,
		OrderManager                   $orderManager,
		CustomerManager                $customerManager,
		CustomerMapping                $customerMapping,
		private ProformaInvoiceManager $proformaInvoiceManager
	) {
		parent::__construct(
			$entityManager,
			$billingMethodMapper,
			$currencyManager,
			$orderManager,
			$customerManager,
			$customerMapping
		);
	}

	public function save(Project $project, \App\DTO\Shoptet\Invoice\Invoice $invoice): Invoice
	{
		/** @var Invoice $document */
		$document = $this->pairByCodeAndProject($project, $invoice->code);
		if ($invoice->changeTime instanceof \DateTimeImmutable) {
			if ($document->getChangeTime() instanceof \DateTimeImmutable && $document->getChangeTime() >= $invoice->changeTime) {
				return $document;
			}
		}

		$this->fillBasicData($document, $invoice);
		$this->fillBillingAddress($document, $invoice);
		$this->fillDeliveryAddress($document, $invoice);
		$this->processItems($document, $invoice);
		$this->fillCustomerData($document, $invoice);
		$document->setDeletedAt(null);
		if ($invoice->proformaInvoiceCode !== null) {
			$existInvoice = $this->proformaInvoiceManager->findByShoptet($project, $invoice->proformaInvoiceCode);
			if ($existInvoice instanceof ProformaInvoice) {
				if (!$existInvoice->getInvoice() instanceof Invoice || $existInvoice->getInvoice()->getShoptetCode() === $invoice->code) {
					$document->setProformaInvoice($existInvoice);
					$existInvoice->setInvoice($document);
				} else {
					$document->setProformaInvoice(null);
				}
				$this->entityManager->persist($existInvoice);
			} else {
				$document->setProformaInvoice(null);
			}
		} else {
			$document->setProformaInvoice(null);
		}


		$document->setTaxDate($invoice->taxDate);
		$document->setProformaInvoiceCode($invoice->proformaInvoiceCode);
		$document->setDocumentRemark($invoice->documentRemark);
		if ($invoice->eetReceipt instanceof EetReceipt) {
			if (!$document->getEet() instanceof InvoiceEET) {
				$document->setEet(new InvoiceEET($document));
				$this->entityManager->persist($document->getEet());
			}
			$eetRecord = $document->getEet();
			$eetRecord->setFirstSent($invoice->eetReceipt->firstSent);
			$eetRecord->setVatId($invoice->eetReceipt->vatId);
			$eetRecord->setRevenueDate($invoice->eetReceipt->revenueDate);
			$eetRecord->setTotalRevenue($invoice->eetReceipt->totalRevenue);
			$eetRecord->setVatBase1($invoice->eetReceipt->vatBase1);
			$eetRecord->setVat1($invoice->eetReceipt->vat1);
			$eetRecord->setVatBase2($invoice->eetReceipt->vatBase2);
			$eetRecord->setVat2($invoice->eetReceipt->vat2);
			$eetRecord->setVatBase3($invoice->eetReceipt->vatBase3);
			$eetRecord->setVat3($invoice->eetReceipt->vat3);
			$eetRecord->setNonTaxableBase($invoice->eetReceipt->nonTaxableBase);
			$eetRecord->setExchangeRate((float)$invoice->eetReceipt->exchangeRate);
			$eetRecord->setPkp($invoice->eetReceipt->pkp);
			$eetRecord->setBkp($invoice->eetReceipt->bkp);
			$eetRecord->setFik($invoice->eetReceipt->fik);
			$eetRecord->setMode($invoice->eetReceipt->mode);
			$eetRecord->setEetMod($invoice->eetReceipt->eetMode);
			$eetRecord->setSent($invoice->eetReceipt->sent);
			$eetRecord->setCashDeskId($invoice->eetReceipt->cashDeskId);
			$eetRecord->setDocumentType($invoice->eetReceipt->documentType);
			$eetRecord->setActive($invoice->eetReceipt->isActive);
		} else {
			if ($document->getEet() instanceof InvoiceEET) {
				$this->entityManager->remove($document->getEet());
				$document->setEet(null);
			}
		}

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
