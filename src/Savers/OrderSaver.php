<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\OrderBillingAddress;
use App\Database\Entity\Shoptet\OrderDeliveryAddress;
use App\Database\Entity\Shoptet\OrderItem;
use App\Database\Entity\Shoptet\OrderPaymentMethods;
use App\Database\Entity\Shoptet\OrderShippingDetail;
use App\Database\Entity\Shoptet\OrderShippingMethods;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\BillingMethod;
use App\DTO\Shoptet\DocumentPrice;
use App\DTO\Shoptet\ItemPrice;
use App\DTO\Shoptet\ItemRecyclingFee;
use App\DTO\Shoptet\OrderStatus;
use App\DTO\Shoptet\ProductMainImage;
use App\Event\NewOrderEvent;
use App\Event\OrderStatusChangeEvent;
use App\Manager\CurrencyManager;
use App\Manager\CustomerManager;
use App\Manager\OrderStatusManager;
use App\Mapping\BillingMethodMapper;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Doctrine\ORM\NoResultException;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Tracy\Debugger;

class OrderSaver
{
	public function __construct(
		protected EntityManager          $entityManager,
		private OrderStatusManager       $orderStatusManager,
		private EventDispatcherInterface $eventDispatcher,
		private CustomerManager          $customerManager,
		private BillingMethodMapper      $billingMethodMapper,
		private CurrencyManager          $currencyManager
	) {
	}

	public function save(Project $project, \App\DTO\Shoptet\Order\Order $order): Order
	{
		$event = null;
		try {
			$document = $this->pairByCodeAndProject($project, $order->code);

			if ($order->changeTime instanceof \DateTimeImmutable) {
				if ($document->getChangeTime() instanceof \DateTimeImmutable && $document->getChangeTime() >= $order->changeTime) {
					return $document;
				}
			}

			if ($document->getStatus()->getShoptetId() !== $order->status->id) {
				$statusEntity = $this->orderStatusManager->findByShoptetId($document->getProject(), $order->status->id);
				$event = new OrderStatusChangeEvent($document, $document->getStatus(), $statusEntity, false);
				$document->setStatus($statusEntity);
			}
		} catch (NoResultException) {
			/** @var Order $className */
			$className = $this->getDocumentClassName();
			$document = new $className($project);
			$document->setCode($order->code);
			$this->entityManager->persist($document);
			$statusEntity = $this->orderStatusManager->findByShoptetId($document->getProject(), $order->status->id);
			$document->setStatus($statusEntity);
			$event = new NewOrderEvent($document);
		}
		$this->fillBasicData($document, $order);

		$this->fillBillingAddress($document, $order);
		$this->fillShippingDetail($document, $order);
		$this->fillDeliveryAddress($document, $order);
		$this->processPaymentMethods($document, $order);
		$this->processShippingMethods($document, $order);
		$this->processItems($document, $order);
		$customer = null;
		if ($order->customerGuid !== null) {
			$customer = $this->customerManager->findByGuid($project, $order->customerGuid);
			if (!$customer instanceof Customer) {
				$customer = $this->customerManager->synchronizeFromShoptet($project, $order->customerGuid);
			}
		}
		if (!$customer instanceof Customer) {
			$customer = $this->customerManager->getEndUser($project);
		}
		$document->setCustomer($customer);
		$customer->getOrders()->add($document);

		$this->entityManager->flush();
		if ($event instanceof Event) {
			$this->eventDispatcher->dispatch($event);
		}

		return $document;
	}


	protected function getDocumentClassName(): string
	{
		return Order::class;
	}

	protected function getItemEntity(): OrderItem
	{
		return new OrderItem();
	}

	protected function getBillingAddressEntity(): OrderBillingAddress
	{
		return new OrderBillingAddress();
	}

	protected function getDeliveryAddressEntity(): OrderDeliveryAddress
	{
		return new OrderDeliveryAddress();
	}

	protected function pairByCodeAndProject(Project $project, string $code): Order
	{
		$qb = $this->entityManager->createQueryBuilder()
			->from($this->getDocumentClassName(), 'd')
			->select('d')
			->addSelect('da')
			->addSelect('db')
			->addSelect('sd')
			->addSelect('i')
			->addSelect('dshipping')
			->addSelect('dpayments')
			->leftJoin('d.deliveryAddress', 'da')
			->leftJoin('d.shippingDetail', 'sd')
			->leftJoin('d.billingAddress', 'db')
			->leftJoin('d.shippings', 'dshipping')
			->leftJoin('d.paymentMethods', 'dpayments')
			->leftJoin('d.items', 'i')
			->where('d.code = :documentCode')
			->andWhere('d.project = :project')
			->setParameter('documentCode', $code)
			->setParameter('project', $project);
		return $qb->getQuery()->getSingleResult();
	}

	protected function processShippingMethods(Order $document, \App\DTO\Shoptet\Order\Order $dtoDocument): void
	{
		$itemIds = [];
		foreach ($dtoDocument->shippings as $item) {
			$itemIds[] = $item->itemId;
		}
		$persistedEntities = [];
		/** @var OrderShippingMethods $entity */
		foreach ($document->getShippings() as $entity) {
			if (!in_array($entity->getItemId(), $itemIds, true)) {
				$document->getShippings()->removeElement($entity);
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getItemId()] = $entity;
		}
		/** @var \App\DTO\Shoptet\Order\OrderShippingMethods $item */
		foreach ($dtoDocument->shippings as $item) {
			if (isset($persistedEntities[$item->itemId])) {
				$entity = $persistedEntities[$item->itemId];
			} else {
				$entity = new OrderShippingMethods();
				$this->entityManager->persist($entity);
				$document->addShippingMethod($entity);
				$entity->setDocument($document);
			}
			$entity->setName($item->shipping->name);
			$entity->setGuid($item->shipping->guid);
			$entity->setItemId($item->itemId);
		}
	}

	protected function processPaymentMethods(Order $document, \App\DTO\Shoptet\Order\Order $dtoDocument): void
	{
		$itemIds = [];
		foreach ($dtoDocument->paymentMethods as $item) {
			$itemIds[] = $item->itemId;
		}
		$persistedEntities = [];
		/** @var OrderPaymentMethods $entity */
		foreach ($document->getPaymentMethods() as $entity) {
			if (!in_array($entity->getItemId(), $itemIds, true)) {
				$document->getPaymentMethods()->removeElement($entity);
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getItemId()] = $entity;
		}
		/** @var \App\DTO\Shoptet\Order\OrderPaymentMethods $item */
		foreach ($dtoDocument->paymentMethods as $item) {
			if (isset($persistedEntities[$item->itemId])) {
				$entity = $persistedEntities[$item->itemId];
			} else {
				$entity = new OrderPaymentMethods();
				$this->entityManager->persist($entity);
				$document->addPaymentMethod($entity);
				$entity->setDocument($document);
			}
			$entity->setName($item->paymentMethod->name);
			$entity->setGuid($item->paymentMethod->guid);
			$entity->setItemId($item->itemId);
		}
	}

	protected function processItems(Order $document, \App\DTO\Shoptet\Order\Order $dtoDocument): void
	{
		$hashes = [];
		foreach ($dtoDocument->items as $item) {
			$hashes[] = $item->getControlHash();
		}
		$persistedEntities = [];
		/** @var OrderItem $entity */
		foreach ($document->getItems() as $entity) {
			if (!in_array($entity->getControlHash(), $hashes, true)) {
				$document->getItems()->removeElement($entity);
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getControlHash()] = $entity;
		}
		/** @var \App\DTO\Shoptet\Order\OrderItem $item */
		foreach ($dtoDocument->items as $item) {
			if (isset($persistedEntities[$item->getControlHash()])) {
				$entity = $persistedEntities[$item->getControlHash()];
			} else {
				$entity = $this->getItemEntity();
				$this->entityManager->persist($entity);
				$document->addItem($entity);
			}


			$entity->setDocument($document);
			$entity->setSupplierName($item->supplierName);
			$entity->setAmountCompleted((float)$item->amountCompleted);
			$entity->setStockLocation($item->stockLocation);
			$entity->setItemId($item->itemId);
			$entity->setWarrantyDescription($item->warrantyDescription);
			$entity->setProductGuid($item->productGuid);
			$entity->setCode($item->code);
			$entity->setItemType($item->itemType);
			$entity->setName($item->name);
			$entity->setVariantName($item->variantName);
			$entity->setBrand($item->brand);
			$entity->setRemark($item->remark);
			$entity->setWeight((float)$item->weight);
			$entity->setAdditionalField($item->additionalField);
			$entity->setAmount((float)$item->amount);
			$entity->setAmountUnit($item->amountUnit);
			$entity->setPriceRatio((float)$item->priceRatio);

			if ($item->itemPrice instanceof ItemPrice) {
				$entity->setItemPriceWithVat((float)$item->itemPrice->withVat);
				$entity->setItemPriceWithoutVat((float)$item->itemPrice->withoutVat);
				$entity->setItemPriceVat((float)$item->itemPrice->vat);
				$entity->setItemPriceVatRate((int)$item->itemPrice->vatRate);

				if ($entity->getAmount() > 1.0) {
					$scale = 5;
					$amount = \Brick\Math\BigDecimal::of($entity->getAmount())
						->toScale($scale);
					if ($entity->getItemPriceWithoutVat() !== null) {
						$entity->setUnitPriceWithoutVat(
							\Brick\Math\BigDecimal::of($entity->getItemPriceWithoutVat())
								->toScale($scale)
								->dividedBy($amount, $scale, RoundingMode::HALF_CEILING)
								->toFloat()
						);
					} else {
						$entity->setUnitPriceWithoutVat(0);
					}
					if ($entity->getItemPriceWithVat() !== null) {
						$entity->setUnitPriceWithVat(
							\Brick\Math\BigDecimal::of($entity->getItemPriceWithVat())
								->toScale($scale)
								->dividedBy($amount, $scale, RoundingMode::HALF_CEILING)
								->toFloat()
						);
					} else {
						$entity->setUnitPriceWithVat(0);
					}
				} else {
					$entity->setUnitPriceWithoutVat((float)$entity->getItemPriceWithoutVat());
					$entity->setUnitPriceWithVat((float)$entity->getItemPriceWithVat());
				}
			} else {
				$entity->setItemPriceWithVat(null);
				$entity->setItemPriceWithoutVat(null);
				$entity->setItemPriceVat(null);
				$entity->setItemPriceVatRate(null);
				$entity->setUnitPriceWithoutVat(null);
				$entity->setUnitPriceWithVat(null);
			}

			if ($item->buyPrice instanceof ItemPrice) {
				$entity->setBuyPriceWithVat((float)$item->buyPrice->withVat);
				$entity->setBuyPriceWithoutVat((float)$item->buyPrice->withoutVat);
				$entity->setBuyPriceVat((float)$item->buyPrice->vat);
				$entity->setBuyPriceVatRate((float)$item->buyPrice->vatRate);
			} else {
				$entity->setBuyPriceWithVat(null);
				$entity->setBuyPriceWithoutVat(null);
				$entity->setBuyPriceVat(null);
				$entity->setBuyPriceVatRate(null);
			}
			if ($item->recyclingFee instanceof ItemRecyclingFee) {
				$entity->setRecyclingFeeCategory($item->recyclingFee->category);
				$entity->setRecyclingFee($item->recyclingFee->fee);
			} else {
				$entity->setRecyclingFeeCategory(null);
				$entity->setRecyclingFee(null);
			}

			if ($item->status instanceof OrderStatus) {
				$entity->setStatusId($item->status->id);
				$entity->setStatusName($item->status->name);
			} else {
				$entity->setStatusId(null);
				$entity->setStatusName(null);
			}

			if ($item->mainImage instanceof ProductMainImage) {
				$entity->setMainImageName($item->mainImage->name);
				$entity->setMainImageNeoName($item->mainImage->seoName);
				$entity->setMainImageCdnName($item->mainImage->cdnName);
				$entity->setMainImagePriority($item->mainImage->priority);
				$entity->setMainImageDescription($item->mainImage->description);
			} else {
				$entity->setMainImageName(null);
				$entity->setMainImageNeoName(null);
				$entity->setMainImageCdnName(null);
				$entity->setMainImagePriority(null);
				$entity->setMainImageDescription(null);
			}

			$entity->setControlHash($item->getControlHash());
		}
	}

	protected function fillShippingDetail(Order $document, \App\DTO\Shoptet\Order\Order $dtoDocument): void
	{
		if ($dtoDocument->shippingDetails === null) {
			if ($document->getShippingDetail() instanceof OrderShippingDetail) {
				$this->entityManager->remove($document->getShippingDetail());
				$document->setShippingDetail(null);
			}
			return;
		}
		if (!$document->getShippingDetail() instanceof OrderShippingDetail) {
			$document->setShippingDetail(new OrderShippingDetail());
			$this->entityManager->persist($document->getShippingDetail());
		}
		/** @var OrderShippingDetail $shippingDetail */
		$shippingDetail = $document->getShippingDetail();

		$shippingDetail->setDocument($document);
		$shippingDetail->setBranchId($dtoDocument->shippingDetails->branchId);
		$shippingDetail->setName($dtoDocument->shippingDetails->name);
		$shippingDetail->setNote($dtoDocument->shippingDetails->note);
		$shippingDetail->setPlace($dtoDocument->shippingDetails->place);
		$shippingDetail->setStreet($dtoDocument->shippingDetails->street);
		$shippingDetail->setCity($dtoDocument->shippingDetails->city);
		$shippingDetail->setZipCode($dtoDocument->shippingDetails->zipCode);
		$shippingDetail->setCountryCode($dtoDocument->shippingDetails->countryCode);
		$shippingDetail->setLink($dtoDocument->shippingDetails->link);
		$shippingDetail->setLatitude($dtoDocument->shippingDetails->latitude);
		$shippingDetail->setLongtitude($dtoDocument->shippingDetails->longtitude);

		//$document->setShippingDetail(OrderShippingDetail $shippingDetail);
	}

	protected function fillDeliveryAddress(Order $document, \App\DTO\Shoptet\Order\Order $dtoDocument): void
	{
		if ($dtoDocument->deliveryAddress === null) {
			if ($document->getDeliveryAddress() instanceof OrderDeliveryAddress) {
				$this->entityManager->remove($document->getDeliveryAddress());
				$document->setDeliveryAddress(null);
			}
			return;
		}
		if (!$document->getDeliveryAddress() instanceof OrderDeliveryAddress) {
			$document->setDeliveryAddress($this->getDeliveryAddressEntity());
			$this->entityManager->persist($document->getDeliveryAddress());
		}
		$address = $document->getDeliveryAddress();

		$address->setDocument($document);
		$address->setCompany($dtoDocument->deliveryAddress->company);
		$address->setFullName($dtoDocument->deliveryAddress->fullName);
		$address->setStreet($dtoDocument->deliveryAddress->street);
		$address->setHouseNumber($dtoDocument->deliveryAddress->houseNumber);
		$address->setCity($dtoDocument->deliveryAddress->city);
		$address->setDistrict($dtoDocument->deliveryAddress->district);
		$address->setAdditional($dtoDocument->deliveryAddress->additional);
		$address->setZip($dtoDocument->deliveryAddress->zip);
		$address->setCountryCode($dtoDocument->deliveryAddress->countryCode);
		$address->setRegionName($dtoDocument->deliveryAddress->regionName);
		$address->setRegionShortcut($dtoDocument->deliveryAddress->regionShortcut);
	}

	protected function fillBillingAddress(Order $document, \App\DTO\Shoptet\Order\Order $dtoDocument): void
	{
		if ($dtoDocument->billingAddress === null) {
			if ($document->getBillingAddress() instanceof OrderBillingAddress) {
				$this->entityManager->remove($document->getBillingAddress());
				$document->setBillingAddress(null);
			}
			return;
		}
		if (!$document->getBillingAddress() instanceof OrderBillingAddress) {
			$document->setBillingAddress($this->getBillingAddressEntity());
			$this->entityManager->persist($document->getBillingAddress());
		}
		$address = $document->getBillingAddress();

		$address->setDocument($document);
		$address->setCompany($dtoDocument->billingAddress->company);
		$address->setFullName($dtoDocument->billingAddress->fullName);
		$address->setStreet($dtoDocument->billingAddress->street);
		$address->setHouseNumber($dtoDocument->billingAddress->houseNumber);
		$address->setCity($dtoDocument->billingAddress->city);
		$address->setDistrict($dtoDocument->billingAddress->district);
		$address->setAdditional($dtoDocument->billingAddress->additional);
		$address->setZip($dtoDocument->billingAddress->zip);
		$address->setCountryCode($dtoDocument->billingAddress->countryCode);
		$address->setRegionName($dtoDocument->billingAddress->regionName);
		$address->setRegionShortcut($dtoDocument->billingAddress->regionShortcut);
	}

	protected function fillBasicData(Order $document, \App\DTO\Shoptet\Order\Order $dtoDocument): void
	{
		$document->setCode($dtoDocument->code);
		$document->setShoptetCode($dtoDocument->code);
		$document->setExternalCode($dtoDocument->externalCode);
		$document->setCreationTime($dtoDocument->creationTime);
		$document->setChangeTime($dtoDocument->changeTime);
		$document->setEmail($dtoDocument->email);
		$document->setPhone($dtoDocument->phone);
		$document->setClientCode($dtoDocument->clientCode);
		$document->setCompanyId($dtoDocument->companyId);
		$document->setVatId($dtoDocument->vatId);
		$document->setTaxId($dtoDocument->taxId);
		$document->setVatPayer($dtoDocument->vatPayer);
		$document->setAddressesEqual($dtoDocument->addressesEqual);
		$document->setCashDeskOrder($dtoDocument->cashDeskOrder);
		$document->setPaid($dtoDocument->paid ?? false);
		$document->setAdminUrl($dtoDocument->adminUrl);
		$document->setLanguage($dtoDocument->language);

		if ($dtoDocument->billingMethod instanceof BillingMethod) {
			$document->setBillingMethodId($dtoDocument->billingMethod->id);
			$document->setBillingMethod(
				$this->billingMethodMapper->getBillingMethod($dtoDocument->billingMethod->id)
			);
		} else {
			$document->setBillingMethodId(null);
			$document->setBillingMethod(null);
		}

		if ($dtoDocument->price instanceof DocumentPrice) {
			$document->setPriceVat((float)$dtoDocument->price->vat);
			$document->setPriceVatRate((float)$dtoDocument->price->vatRate);
			$document->setPriceToPay((float)$dtoDocument->price->toPay);
			$document->setPriceCurrencyCode($dtoDocument->price->currencyCode);
			$document->setCurrency(
				$this->currencyManager->getByCurrency(
					$document->getProject(),
					$document->getPriceCurrencyCode(),
					$document->isCashDeskOrder()
				)
			);

			$document->setPriceWithVat((float)$dtoDocument->price->withVat);
			$document->setPriceWithoutVat((float)$dtoDocument->price->withoutVat);
			$document->setMainPriceWithVat((float)$dtoDocument->price->withVat);
			$document->setMainPriceWithoutVat((float)$dtoDocument->price->withoutVat);
			$document->setPriceExchangeRate((float)$dtoDocument->price->exchangeRate);

			try {
				$exchangeRate = (float)$dtoDocument->price->exchangeRate;
				if ($exchangeRate > 0.0 && $document->getPriceWithoutVat() !== null && $document->getPriceWithoutVat() > 0.0) {
					$scale = 4;

					$priceWithoutVat = BigDecimal::of($document->getPriceWithoutVat())->toScale($scale, RoundingMode::HALF_CEILING);
					$orderExchangeRate = BigDecimal::of($exchangeRate)->toScale($scale, RoundingMode::HALF_CEILING);
					$temp = $priceWithoutVat->dividedBy($orderExchangeRate, $scale, RoundingMode::HALF_CEILING);
					$finaleExchangeRate = $temp->dividedBy($priceWithoutVat, $scale, RoundingMode::HALF_CEILING);
					$document->setPriceExchangeRate($finaleExchangeRate->toFloat());

					$withoutVat = BigDecimal::of($document->getPriceWithoutVat())->toScale($scale, RoundingMode::HALF_CEILING);
					$withVat = BigDecimal::of($document->getPriceWithVat())->toScale($scale, RoundingMode::HALF_CEILING);
					$document->setMainPriceWithoutVat(
						$withoutVat->multipliedBy($finaleExchangeRate)
							->toScale($scale, RoundingMode::HALF_CEILING)
							->toFloat()
					);
					$document->setMainPriceWithVat(
						$withVat->multipliedBy($finaleExchangeRate)
							->toScale($scale, RoundingMode::HALF_CEILING)
							->toFloat()
					);
				}
			} catch (\Throwable $exception) {
				Debugger::log($exception);
				throw $exception;
			}
		} else {
			$document->setPriceVat(null);
			$document->setPriceVatRate(null);
			$document->setPriceToPay(null);

			$document->setCurrency(
				$this->currencyManager->getDefaultCurrency(
					$document->getProject(),
					$document->isCashDeskOrder()
				)
			);
			$document->setPriceCurrencyCode($document->getCurrency()->getCode());
			$document->setPriceWithVat(null);
			$document->setPriceWithoutVat(null);
			$document->setPriceExchangeRate(null);
			$document->setMainPriceWithVat(null);
			$document->setMainPriceWithoutVat(null);
		}
	}
}
