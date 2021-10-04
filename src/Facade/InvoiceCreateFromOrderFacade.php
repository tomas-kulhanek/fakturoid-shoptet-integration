<?php

declare(strict_types=1);


namespace App\Facade;

use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceBillingAddress;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\OrderBillingAddress;
use App\Database\Entity\Shoptet\OrderDeliveryAddress;
use App\Database\Entity\Shoptet\OrderItem;
use App\Database\EntityManager;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class InvoiceCreateFromOrderFacade
{
	public function __construct(
		protected EntityManager $entityManager,
		protected EventDispatcherInterface $eventDispatcher
	) {
	}

	public function create(Order $order): Invoice
	{
		$invoice = new Invoice($order->getProject());
		$this->entityManager->persist($invoice);
		$invoice->setOrderCode($order->getCode());
		$invoice->setOrder($order);
		$invoice->setWithVat($order->getPriceWithVat());
		$invoice->setWithoutVat($order->getPriceWithoutVat());
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
		$invoice->setBillingMethodName($order->getBillingMethodName());
		$invoice->setCreationTime(new \DateTimeImmutable());
		$invoice->setCode('');
		$invoice->setIsValid(false);
		$invoice->setVarSymbol(null);
		$invoice->setVat($order->getPriceVat());
		$invoice->setVatRate($order->getVatId());
		$invoice->setCurrencyCode($order->getPriceCurrencyCode());
		$invoice->setToPay($order->getPriceToPay());
		$invoice->setExchangeRate($order->getPriceExchangeRate());
		$invoice->setPaid(false);
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
			$this->entityManager->persist($invoiceItem);
		}

		$this->entityManager->flush();
		return $invoice;
	}
}
