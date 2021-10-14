<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\DocumentAddress;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceBillingAddress;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Manager\InvoiceManager;
use App\Manager\OrderManager;
use App\Manager\ProformaInvoiceManager;
use App\Mapping\BillingMethodMapper;

class InvoiceSaver extends DocumentSaver
{
	public function __construct(
		EntityManager                  $entityManager,
		BillingMethodMapper $billingMethodMapper,
		private OrderManager           $orderManager,
		private ProformaInvoiceManager $proformaInvoiceManager
	) {
		parent::__construct($entityManager, $billingMethodMapper);
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

		if ($invoice->orderCode !== null) {
			$existsOrder = $this->orderManager->findByShoptet($project, $invoice->orderCode);
			if ($existsOrder instanceof Order) {
				$document->setOrder($existsOrder);
				$existsOrder->getInvoices()->add($document);
			} else {
				$document->setOrder(null);
			}
		} else {
			$document->setOrder(null);
		}
		if ($invoice->proformaInvoiceCode !== null) {
			$existInvoice = $this->proformaInvoiceManager->findByShoptet($project, $invoice->proformaInvoiceCode);
			if ($existInvoice instanceof ProformaInvoice) {
				$document->setProformaInvoice($existInvoice);
				$existInvoice->setInvoice($document);
			} else {
				$document->setProformaInvoice(null);
			}
		} else {
			$document->setProformaInvoice(null);
		}


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
