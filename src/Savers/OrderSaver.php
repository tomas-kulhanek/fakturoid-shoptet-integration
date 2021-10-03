<?php

declare(strict_types=1);


namespace App\Savers;

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
use Doctrine\ORM\NoResultException;

class OrderSaver
{
	public function __construct(
		protected EntityManager $entityManager
	)
	{
	}

	public function save(Project $project, \App\DTO\Shoptet\Order\Order $order): Order
	{
		$document = $this->pairByCodeAndProject($project, $order->code);
		if ($order->changeTime instanceof \DateTimeImmutable) {
			if ($document->getChangeTime() instanceof \DateTimeImmutable && $document->getChangeTime() >= $order->changeTime) {
				//return $document;
			}
		}

		$this->fillBasicData($document, $order);
		$this->fillBillingAddress($document, $order);
		$this->fillShippingDetail($document, $order);
		$this->fillDeliveryAddress($document, $order);
		$this->processPaymentMethods($document, $order);
		$this->processShippingMethods($document, $order);
		$this->processItems($document, $order);

		$this->entityManager->flush();

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
		try {
			$document = $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			$className = $this->getDocumentClassName();
			$document = new $className($project);
			$document->setCode($code);
			$this->entityManager->persist($document);
		}

		return $document;
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
			$entity->setAmountCompleted($item->amountCompleted);
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
			$entity->setWeight($item->weight);
			$entity->setAdditionalField($item->additionalField);
			$entity->setAmount($item->amount);
			$entity->setAmountUnit($item->amountUnit);
			$entity->setPriceRatio($item->priceRatio);

			if ($item->itemPrice instanceof ItemPrice) {
				$entity->setItemPriceWithVat($item->itemPrice->withVat);
				$entity->setItemPriceWithoutVat($item->itemPrice->withoutVat);
				$entity->setItemPriceVat($item->itemPrice->vat);
				$entity->setItemPriceVatRate($item->itemPrice->vatRate);
			} else {
				$entity->setItemPriceWithVat(null);
				$entity->setItemPriceWithoutVat(null);
				$entity->setItemPriceVat(null);
				$entity->setItemPriceVatRate(null);
			}

			if ($item->buyPrice instanceof ItemPrice) {
				$entity->setBuyPriceWithVat($item->buyPrice->withVat);
				$entity->setBuyPriceWithoutVat($item->buyPrice->withoutVat);
				$entity->setBuyPriceVat($item->buyPrice->vat);
				$entity->setBuyPriceVatRate($item->buyPrice->vatRate);
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
		$document->setExternalCode($dtoDocument->externalCode);
		$document->setCreationTime($dtoDocument->creationTime);
		$document->setChangeTime($dtoDocument->changeTime);
		$document->setEmail($dtoDocument->email);
		$document->setPhone($dtoDocument->phone);
		$document->setBirthDate($dtoDocument->birthDate);
		$document->setClientCode($dtoDocument->clientCode);
		$document->setCompanyId($dtoDocument->companyId);
		$document->setVatId($dtoDocument->vatId);
		$document->setTaxId($dtoDocument->taxId);
		$document->setVatPayer($dtoDocument->vatPayer);
		$document->setCustomerGuid($dtoDocument->customerGuid);
		$document->setAddressesEqual($dtoDocument->addressesEqual);
		$document->setCashDeskOrder($dtoDocument->cashDeskOrder);
		$document->setStockId($dtoDocument->stockId);
		$document->setPaid($dtoDocument->paid ?? false);
		$document->setAdminUrl($dtoDocument->adminUrl);
		$document->setOnlinePaymentLink($dtoDocument->onlinePaymentLink);
		$document->setLanguage($dtoDocument->language);
		$document->setReferer($dtoDocument->referer);
		$document->setClientIPAddress($dtoDocument->clientIPAddress);

		$document->setStatusId($dtoDocument->status->id);
		$document->setStatusName($dtoDocument->status->name);

		if ($dtoDocument->billingMethod instanceof BillingMethod) {
			$document->setBillingMethodId($dtoDocument->billingMethod->id);
			$document->setBillingMethodName($dtoDocument->billingMethod->name);
		} else {
			$document->setBillingMethodId(null);
			$document->setBillingMethodName(null);
		}

		if ($dtoDocument->price instanceof DocumentPrice) {
			$document->setPriceVat($dtoDocument->price->vat);
			$document->setPriceVatRate($dtoDocument->price->vatRate);
			$document->setPriceToPay($dtoDocument->price->toPay);
			$document->setPriceCurrencyCode($dtoDocument->price->currencyCode);
			$document->setPriceWithVat($dtoDocument->price->withVat);
			$document->setPriceWithoutVat($dtoDocument->price->withoutVat);
			$document->setPriceExchangeRate($dtoDocument->price->exchangeRate);
		} else {
			$document->setPriceVat(null);
			$document->setPriceVatRate(null);
			$document->setPriceToPay(null);
			$document->setPriceCurrencyCode(null);
			$document->setPriceWithVat(null);
			$document->setPriceWithoutVat(null);
			$document->setPriceExchangeRate(null);
		}
	}
}
