<?php

declare(strict_types=1);


namespace App\Facade;

use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Currency;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\OrderBillingAddress;
use App\Database\Entity\Shoptet\OrderDeliveryAddress;
use App\Database\Entity\Shoptet\OrderItem;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoiceBillingAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceItem;
use App\Database\EntityManager;
use App\Facade\Fakturoid;
use App\Log\ActionLog;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ProformaInvoiceCreateFacade
{
	public function __construct(
		protected EntityManager             $entityManager,
		protected ActionLog                 $actionLog,
		protected EventDispatcherInterface  $eventDispatcher,
		protected Fakturoid\ProformaInvoice $fakturoidProformaInvoice
	) {
	}


	protected function getRoundingMode(Currency $currency): int
	{
		if ($currency->getRounding() === 'up') {
			return RoundingMode::UP;
		}
		if ($currency->getRounding() === 'down') {
			return RoundingMode::DOWN;
		}
		if ($currency->getRounding() === 'math') {
			return RoundingMode::HALF_CEILING;
		}

		return RoundingMode::UNNECESSARY;
	}

	/**
	 * @param int[] $items
	 */
	public function createFromOrder(Order $order, array $items = []): ProformaInvoice
	{
		$invoice = new ProformaInvoice($order->getProject());
		$this->entityManager->persist($invoice);
		$invoice->setOrderCode($order->getCode());
		$invoice->setOrder($order);
		$invoice->setWithVat($order->getPriceWithVat());
		$invoice->setWithoutVat($order->getPriceWithoutVat());
		$invoice->setVatId($order->getVatId());
		$invoice->setVatPayer($order->getVatPayer());
		$invoice->setCompanyId($order->getCompanyId());
		$invoice->setTaxId($order->getTaxId());
		$invoice->setCurrency($order->getCurrency());
		$invoice->setCustomer($order->getCustomer());
		$invoice->setEmail($order->getEmail());
		$invoice->setPhone($order->getPhone());
		if ($order->getDeliveryAddress() instanceof OrderDeliveryAddress) {
			$invoice->setDeliveryAddress(new ProformaInvoiceDeliveryAddress());
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
			$invoice->setBillingAddress(new ProformaInvoiceBillingAddress());
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
		$invoice->setVatRate((int)$order->getPriceVatRate());
		$invoice->setCurrencyCode($order->getPriceCurrencyCode());
		$invoice->setToPay($order->getPriceToPay());
		$invoice->setExchangeRate($order->getPriceExchangeRate());
		$invoice->setPaid(false);

		$withoutVat = 0;
		$withVat = 0;
		/** @var OrderItem $item */
		foreach ($order->getItems()->filter(fn (OrderItem $orderItem) => in_array($orderItem->getId(), $items, true)) as $item) {
			$invoiceItem = new ProformaInvoiceItem();
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
			$item->setAccounted(true);

			$withoutVat += $item->getItemPriceWithoutVat();
			$withVat += $item->getItemPriceWithVat();
			$this->entityManager->persist($invoiceItem);
		}


		$invoice->setWithVat($withVat);
		$invoice->setWithoutVat($withoutVat);
		$invoice->setToPay($invoice->getWithVat());


		$exchangeRate = (float)$invoice->getExchangeRate();
		if ($exchangeRate > 0.0 && $invoice->getWithoutVat() !== null && $invoice->getWithoutVat() > 0.0) {
			$scale = 4;

			$withoutVat = BigDecimal::of($invoice->getWithoutVat())->toScale($scale, $this->getRoundingMode($invoice->getCurrency()));
			$withVat = BigDecimal::of($invoice->getWithVat())->toScale($scale, $this->getRoundingMode($invoice->getCurrency()));
			$invoice->setMainWithoutVat(
				$withoutVat->multipliedBy($exchangeRate)
					->toScale($scale, $this->getRoundingMode($invoice->getCurrency()))
					->toFloat()
			);
			$invoice->setMainWithVat(
				$withVat->multipliedBy($exchangeRate)
					->toScale($scale, $this->getRoundingMode($invoice->getCurrency()))
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

		$this->actionLog->logProformaInvoice($invoice->getProject(), ActionLog::CREATE_PROFORMA, $invoice, null, null, false, false);

		if ($order->getProject()->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
			$this->fakturoidProformaInvoice->create($invoice, false);
		}
		$this->entityManager->flush();

		return $invoice;
	}
}
