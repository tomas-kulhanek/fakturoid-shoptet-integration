<?php

declare(strict_types=1);


namespace App\Facade;

use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceBillingAddress;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\OrderBillingAddress;
use App\Database\Entity\Shoptet\OrderDeliveryAddress;
use App\Database\Entity\Shoptet\OrderItem;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoiceBillingAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\EntityManager;
use App\Log\ActionLog;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InvoiceCreateFacade
{
	public function __construct(
		protected EntityManager                 $entityManager,
		protected ActionLog                     $actionLog,
		protected EventDispatcherInterface      $eventDispatcher,
		protected \App\Facade\Fakturoid\Invoice $fakturoidInvoice
	) {
	}

	public function createFromOrder(Order $order): Invoice
	{
		$invoice = new Invoice($order->getProject());
		$this->entityManager->persist($invoice);
		$invoice->setOrderCode($order->getCode());
		$invoice->setOrder($order);
		$invoice->setVatId($order->getVatId());
		$invoice->setVatPayer($order->getVatPayer());
		$invoice->setCompanyId($order->getCompanyId());
		$invoice->setTaxId($order->getTaxId());
		$invoice->setCurrency($order->getCurrency());
		$invoice->setCustomer($order->getCustomer());
		$invoice->setEmail($order->getEmail());
		$invoice->setPhone($order->getPhone());
		if ($order->getDeliveryAddress() instanceof OrderDeliveryAddress) {
			$invoice->setDeliveryAddress(new InvoiceDeliveryAddress());
			$this->entityManager->persist($invoice->getDeliveryAddress());
			$invoice->getDeliveryAddress()->setDocument($invoice);
			$invoice->getDeliveryAddress()->setCompany($order->getDeliveryAddress()->getCompany());
			$invoice->getDeliveryAddress()->setAdditional($order->getDeliveryAddress()->getAdditional());
			$invoice->getDeliveryAddress()->setCity($order->getDeliveryAddress()->getCity());
			$invoice->getDeliveryAddress()->setCountryCode($order->getDeliveryAddress()->getCountryCode());
			$invoice->getDeliveryAddress()->setFullName($order->getDeliveryAddress()->getFullName());
			$invoice->getDeliveryAddress()->setHouseNumber($order->getDeliveryAddress()->getHouseNumber());
			$invoice->getDeliveryAddress()->setRegionName($order->getDeliveryAddress()->getRegionName());
			$invoice->getDeliveryAddress()->setZip($order->getDeliveryAddress()->getZip());
			$invoice->getDeliveryAddress()->setRegionShortcut($order->getDeliveryAddress()->getRegionShortcut());
			$invoice->getDeliveryAddress()->setStreet($order->getDeliveryAddress()->getStreet());
		}
		if ($order->getBillingAddress() instanceof OrderBillingAddress) {
			$invoice->setBillingAddress(new InvoiceBillingAddress());
			$this->entityManager->persist($invoice->getBillingAddress());
			$invoice->getBillingAddress()->setDocument($invoice);
			$invoice->getBillingAddress()->setCompany($order->getBillingAddress()->getCompany());
			$invoice->getBillingAddress()->setAdditional($order->getBillingAddress()->getAdditional());
			$invoice->getBillingAddress()->setCity($order->getBillingAddress()->getCity());
			$invoice->getBillingAddress()->setCountryCode($order->getBillingAddress()->getCountryCode());
			$invoice->getBillingAddress()->setFullName($order->getBillingAddress()->getFullName());
			$invoice->getBillingAddress()->setHouseNumber($order->getBillingAddress()->getHouseNumber());
			$invoice->getBillingAddress()->setRegionName($order->getBillingAddress()->getRegionName());
			$invoice->getBillingAddress()->setZip($order->getBillingAddress()->getZip());
			$invoice->getBillingAddress()->setRegionShortcut($order->getBillingAddress()->getRegionShortcut());
			$invoice->getBillingAddress()->setStreet($order->getBillingAddress()->getStreet());
		}
		$invoice->setAddressesEqual($order->isAddressesEqual());
		$invoice->setBillingMethodId($order->getBillingMethodId());
		$invoice->setBillingMethod($order->getBillingMethod());
		$invoice->setCreationTime(new \DateTimeImmutable());
		$invoice->setCode('');
		$invoice->setIsValid(false);
		$invoice->setVarSymbol(null);
		$invoice->setVat($order->getPriceVat());
		$invoice->setVatRate($order->getPriceVatRate());
		$invoice->setCurrencyCode($order->getPriceCurrencyCode());
		$invoice->setToPay($order->getPriceToPay());
		$invoice->setExchangeRate($order->getPriceExchangeRate());
		$invoice->setPaid(false);


		$withoutVat = 0;
		$withVat = 0;
		//foreach ($order->getItems()->filter(fn (OrderItem $orderItem) => in_array($orderItem->getId(), $items, true)) as $item) {
		/** @var OrderItem $item */
		foreach ($order->getItems() as $item) {
			$invoiceItem = new InvoiceItem();
			$invoice->getItems()->add($invoiceItem);
			$invoiceItem->setDocument($invoice);
			$invoiceItem->setProductGuid($item->getProductGuid());
			$invoiceItem->setItemType($item->getItemType());
			$invoiceItem->setCode($item->getCode());
			$invoiceItem->setName($item->getName());
			$invoiceItem->setVariantName($item->getVariantName());
			$invoiceItem->setBrand($item->getBrand());
			$invoiceItem->setAmount($item->getAmount());
			$invoiceItem->setAmountUnit($item->getAmountUnit());
			$invoiceItem->setWeight($item->getWeight());
			$invoiceItem->setRemark($item->getRemark());
			$invoiceItem->setPriceRatio($item->getPriceRatio());
			$invoiceItem->setAdditionalField($item->getAdditionalField());
			$invoiceItem->setWithVat($item->getItemPriceWithVat());
			$invoiceItem->setWithoutVat($item->getItemPriceWithoutVat());
			$invoiceItem->setVat($item->getItemPriceVat());
			$invoiceItem->setVatRate($item->getItemPriceVatRate());
			$invoiceItem->setControlHash($item->getControlHash());
			$invoiceItem->setUnitWithoutVat($item->getUnitPriceWithoutVat());
			$invoiceItem->setUnitWithVat($item->getUnitPriceWithVat());
			$item->setAccounted(true);

			$withoutVat += $item->getItemPriceWithoutVat();
			$withVat += $item->getItemPriceWithVat();
			$this->entityManager->persist($invoiceItem);
		}


		$invoice->setWithVat($withVat);
		$invoice->setWithoutVat($withoutVat);
		$invoice->setToPay($invoice->getWithVat());


		$exchangeRate = (float) $invoice->getExchangeRate();
		if ($exchangeRate > 0.0 && $invoice->getWithoutVat() !== null && $invoice->getWithoutVat() > 0.0) {
			$scale = 4;

			$withoutVat = BigDecimal::of($invoice->getWithoutVat())->toScale($scale, RoundingMode::HALF_CEILING);
			$withVat = BigDecimal::of($invoice->getWithVat())->toScale($scale, RoundingMode::HALF_CEILING);
			$invoice->setMainWithoutVat(
				$withoutVat->multipliedBy($exchangeRate)
					->toScale($scale, RoundingMode::HALF_CEILING)
					->toFloat()
			);
			$invoice->setMainWithVat(
				$withVat->multipliedBy($exchangeRate)
					->toScale($scale, RoundingMode::HALF_CEILING)
					->toFloat()
			);
			$invoice->setMainToPay($invoice->getMainWithVat());
		} else {
			$invoice->setMainWithoutVat(
				$invoice->getWithoutVat()
			);
			$invoice->setMainWithVat(
				$invoice->getWithVat()
			);
			$invoice->setMainToPay(
				$invoice->getWithVat()
			);
		}
		$this->actionLog->logInvoice($invoice->getProject(), ActionLog::CREATE_INVOICE, $invoice, null, null, false, false);

		if ($order->getProject()->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
			$this->fakturoidInvoice->create($invoice, false);
		}
		$this->entityManager->flush();
		return $invoice;
	}

	public function createFromProforma(ProformaInvoice $proforma): Invoice
	{
		$invoice = new Invoice($proforma->getProject());
		$this->entityManager->persist($invoice);
		$invoice->setOrderCode($proforma->getOrder()->getCode());
		$invoice->setOrder($proforma->getOrder());
		$invoice->setProformaInvoice($proforma);
		$proforma->setInvoice($invoice);
		$invoice->setPaid(true);
		$invoice->setProformaInvoiceCode($proforma->getCode());

		$invoice->setCurrency($proforma->getCurrency());
		$invoice->setVatId($proforma->getVatId());
		$invoice->setVatPayer($proforma->getVatPayer());
		$invoice->setCompanyId($proforma->getCompanyId());
		$invoice->setTaxId($proforma->getTaxId());
		$invoice->setCustomer($proforma->getCustomer());
		$invoice->setEmail($proforma->getEmail());
		$invoice->setPhone($proforma->getPhone());
		if ($proforma->getDeliveryAddress() instanceof ProformaInvoiceDeliveryAddress) {
			$invoice->setDeliveryAddress(new InvoiceDeliveryAddress());
			$this->entityManager->persist($invoice->getDeliveryAddress());
			$invoice->getDeliveryAddress()->setDocument($invoice);
			$invoice->getDeliveryAddress()->setCompany($proforma->getDeliveryAddress()->getCompany());
			$invoice->getDeliveryAddress()->setAdditional($proforma->getDeliveryAddress()->getAdditional());
			$invoice->getDeliveryAddress()->setCity($proforma->getDeliveryAddress()->getCity());
			$invoice->getDeliveryAddress()->setCountryCode($proforma->getDeliveryAddress()->getCountryCode());
			$invoice->getDeliveryAddress()->setFullName($proforma->getDeliveryAddress()->getFullName());
			$invoice->getDeliveryAddress()->setHouseNumber($proforma->getDeliveryAddress()->getHouseNumber());
			$invoice->getDeliveryAddress()->setRegionName($proforma->getDeliveryAddress()->getRegionName());
			$invoice->getDeliveryAddress()->setZip($proforma->getDeliveryAddress()->getZip());
			$invoice->getDeliveryAddress()->setRegionShortcut($proforma->getDeliveryAddress()->getRegionShortcut());
			$invoice->getDeliveryAddress()->setStreet($proforma->getDeliveryAddress()->getStreet());
		}
		if ($proforma->getBillingAddress() instanceof ProformaInvoiceBillingAddress) {
			$invoice->setBillingAddress(new InvoiceBillingAddress());
			$this->entityManager->persist($invoice->getBillingAddress());
			$invoice->getBillingAddress()->setDocument($invoice);
			$invoice->getBillingAddress()->setCompany($proforma->getBillingAddress()->getCompany());
			$invoice->getBillingAddress()->setAdditional($proforma->getBillingAddress()->getAdditional());
			$invoice->getBillingAddress()->setCity($proforma->getBillingAddress()->getCity());
			$invoice->getBillingAddress()->setCountryCode($proforma->getBillingAddress()->getCountryCode());
			$invoice->getBillingAddress()->setFullName($proforma->getBillingAddress()->getFullName());
			$invoice->getBillingAddress()->setHouseNumber($proforma->getBillingAddress()->getHouseNumber());
			$invoice->getBillingAddress()->setRegionName($proforma->getBillingAddress()->getRegionName());
			$invoice->getBillingAddress()->setZip($proforma->getBillingAddress()->getZip());
			$invoice->getBillingAddress()->setRegionShortcut($proforma->getBillingAddress()->getRegionShortcut());
			$invoice->getBillingAddress()->setStreet($proforma->getBillingAddress()->getStreet());
		}
		$invoice->setAddressesEqual($proforma->isAddressesEqual());
		$invoice->setBillingMethodId($proforma->getBillingMethodId());
		$invoice->setBillingMethod($proforma->getBillingMethod());
		$invoice->setCreationTime(new \DateTimeImmutable());
		$invoice->setCode('');
		$invoice->setIsValid(false);
		$invoice->setVarSymbol(null);
		$invoice->setVat($proforma->getVat());
		$invoice->setVatRate($proforma->getVatRate());
		$invoice->setCurrencyCode($proforma->getCurrencyCode());
		$invoice->setToPay($proforma->getToPay());
		$invoice->setExchangeRate($proforma->getExchangeRate());
		$invoice->setPaid(false);

		/** @var DocumentItem $item */
		foreach ($proforma->getItems() as $item) {
			$invoiceItem = new InvoiceItem();
			$invoice->getItems()->add($invoiceItem);
			$invoiceItem->setDocument($invoice);
			$invoiceItem->setProductGuid($item->getProductGuid());
			$invoiceItem->setItemType($item->getItemType());
			$invoiceItem->setCode($item->getCode());
			$invoiceItem->setName($item->getName());
			$invoiceItem->setVariantName($item->getVariantName());
			$invoiceItem->setBrand($item->getBrand());
			$invoiceItem->setAmount($item->getAmount());
			$invoiceItem->setAmountUnit($item->getAmountUnit());
			$invoiceItem->setWeight($item->getWeight());
			$invoiceItem->setRemark($item->getRemark());
			$invoiceItem->setPriceRatio($item->getPriceRatio());
			$invoiceItem->setAdditionalField($item->getAdditionalField());
			$invoiceItem->setWithVat($item->getWithVat());
			$invoiceItem->setWithoutVat($item->getWithoutVat());
			$invoiceItem->setVat($item->getVat());
			$invoiceItem->setVatRate($item->getVatRate());
			$invoiceItem->setControlHash($item->getControlHash());
			$invoiceItem->setUnitWithoutVat($item->getUnitWithoutVat());
			$invoiceItem->setUnitWithVat($item->getUnitWithVat());
			$invoiceItem->setAccountingId($item->getAccountingId());

			$this->entityManager->persist($invoiceItem);
		}


		$invoice->setWithVat($proforma->getWithVat());
		$invoice->setWithoutVat($proforma->getWithoutVat());
		$invoice->setToPay($proforma->isPaid() ? 0 : $proforma->getToPay());
		$invoice->setMainToPay($proforma->isPaid() ? 0 : $proforma->getMainToPay());


		$invoice->setMainWithoutVat(
			$proforma->getMainWithoutVat()
		);
		$invoice->setMainWithVat(
			$proforma->getMainWithVat()
		);

		$invoice->changeGuid($proforma->getGuid());
		$this->actionLog->logInvoice($invoice->getProject(), ActionLog::CREATE_INVOICE, $invoice, null, null, false, false);
		$this->entityManager->flush();
		return $invoice;
	}
}
