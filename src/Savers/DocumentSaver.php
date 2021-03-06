<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\Currency;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\DocumentAddress;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\BillingMethod;
use App\DTO\Shoptet\Document as DTODocument;
use App\DTO\Shoptet\ItemPrice;
use App\DTO\Shoptet\ItemRecyclingFee;
use App\Manager\CurrencyManager;
use App\Manager\CustomerManager;
use App\Manager\OrderManager;
use App\Mapping\BillingMethodMapper;
use App\Mapping\CustomerMapping;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Doctrine\ORM\NoResultException;
use Tracy\Debugger;

abstract class DocumentSaver
{
	public function __construct(
		protected EntityManager       $entityManager,
		protected BillingMethodMapper $billingMethodMapper,
		protected CurrencyManager     $currencyManager,
		private CustomerManager       $customerManager,
		private CustomerMapping       $customerMapping,
		private OrderManager          $orderManager
	) {
	}

	abstract protected function getDocumentClassName(): string;

	abstract protected function getBillingAddressEntity(): DocumentAddress;

	abstract protected function getDeliveryAddressEntity(): DocumentAddress;

	abstract protected function getItemEntity(): DocumentItem;

	protected function pairByCodeAndProject(Project $project, string $code): Document
	{
		$qb = $this->entityManager->getRepository($this->getDocumentClassName())
			->createQueryBuilder('d')
			->select('d')
			->addSelect('da')
			->addSelect('db')
			->leftJoin('d.deliveryAddress', 'da')
			->leftJoin('d.billingAddress', 'db')
			->where('d.shoptetCode = :documentCode')
			->andWhere('d.deletedAt IS NULL')
			->andWhere('d.project = :project')
			->setParameter('documentCode', $code)
			->setParameter('project', $project);
		try {
			$document = $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			/** @var Document $className */
			$className = $this->getDocumentClassName();
			$document = new $className($project);
			$document->setCode($code);
			$this->entityManager->persist($document);
		}

		return $document;
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

	protected function processItems(Document $document, DTODocument $dtoDocument): void
	{
		$hashes = [];
		foreach ($dtoDocument->items as $item) {
			$hashes[] = $item->getControlHash();
		}
		$persistedEntities = [];
		/** @var DocumentItem $entity */
		foreach ($document->getItems() as $entity) {
			if (!in_array($entity->getControlHash(), $hashes, true)) {
				$entity->setDeletedAt(new \DateTimeImmutable());
				continue;
			}
			if ($entity->getDeletedAt() instanceof \DateTimeImmutable) {
				$entity->setAccountingId(null);
			}
			$entity->setDeletedAt(null);
			$persistedEntities[$entity->getControlHash()] = $entity;
		}
		foreach ($dtoDocument->items as $item) {
			if (isset($persistedEntities[$item->getControlHash()])) {
				$entity = $persistedEntities[$item->getControlHash()];
			} else {
				$entity = $this->getItemEntity();
				$this->entityManager->persist($entity);
				$document->addItem($entity);
			}

			$entity->setDocument($document);
			$entity->setProductGuid($item->productGuid);
			$entity->setItemType($item->itemType);
			$entity->setCode($item->code);
			$entity->setName($item->name);
			$entity->setVariantName($item->variantName);
			$entity->setBrand($item->brand);
			$entity->setAmount((float)$item->amount);
			$entity->setAmountUnit($item->amountUnit);
			$entity->setWeight((float)$item->weight);
			$entity->setRemark($item->remark);
			$entity->setPriceRatio((float)$item->priceRatio);
			$entity->setAdditionalField($item->additionalField);
			if ($item->itemPrice instanceof ItemPrice) {
				$entity->setWithVat((float)$item->itemPrice->withVat);
				$entity->setWithoutVat((float)$item->itemPrice->withoutVat);
				$entity->setVat((float)$item->itemPrice->vat);
				$entity->setVatRate((int)$item->itemPrice->vatRate);

				if ($entity->getAmount() !== 0.0) {
					$scale = 5;
					$amount = \Brick\Math\BigDecimal::of($entity->getAmount())
						->toScale($scale);
					if ($entity->getWithoutVat() !== null) {
						$entity->setUnitWithoutVat(
							\Brick\Math\BigDecimal::of($entity->getWithoutVat())
								->toScale($scale)
								->dividedBy($amount, $scale, $this->getRoundingMode($document->getCurrency()))
								->toFloat()
						);
					} else {
						$entity->setUnitWithoutVat(0);
					}
					if ($entity->getWithVat() !== null) {
						$entity->setUnitWithVat(
							\Brick\Math\BigDecimal::of($entity->getWithVat())
								->toScale($scale)
								->dividedBy($amount, $scale, $this->getRoundingMode($document->getCurrency()))
								->toFloat()
						);
					} else {
						$entity->setUnitWithVat(0);
					}
				} else {
					$entity->setUnitWithoutVat((float) $entity->getWithoutVat());
					$entity->setUnitWithVat((float) $entity->getWithVat());
				}
			} else {
				$entity->setWithVat(null);
				$entity->setWithoutVat(null);
				$entity->setVat(null);
				$entity->setVatRate(null);
				$entity->setUnitWithVat(0);
				$entity->setUnitWithoutVat(0);
			}
			$entity->setControlHash($item->getControlHash());

			if ($item->recyclingFee instanceof ItemRecyclingFee) {
				$entity->setRecyclingFeeCategory($item->recyclingFee->category);
				$entity->setRecyclingFee($item->recyclingFee->fee);
			} else {
				$entity->setRecyclingFeeCategory(null);
				$entity->setRecyclingFee(null);
			}
		}
	}

	protected function fillDeliveryAddress(Document $document, DTODocument $dtoDocument): void
	{
		if ($dtoDocument->deliveryAddress === null) {
			if ($document->getDeliveryAddress() instanceof DocumentAddress) {
				$this->entityManager->remove($document->getDeliveryAddress());
				$document->setDeliveryAddress(null);
			}

			return;
		}
		if (!$document->getDeliveryAddress() instanceof DocumentAddress) {
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

	protected function fillBillingAddress(Document $document, DTODocument $dtoDocument): void
	{
		if ($dtoDocument->billingAddress === null) {
			if ($document->getBillingAddress() instanceof DocumentAddress) {
				$this->entityManager->remove($document->getBillingAddress());
				$document->setBillingAddress(null);
			}

			return;
		}
		if (!$document->getBillingAddress() instanceof DocumentAddress) {
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

	protected function fillCustomerData(Document $document, DTODocument $dtoDocument): void
	{
		$customer = null;
		$project = $document->getProject();
		if ($dtoDocument->customer instanceof \App\DTO\Shoptet\Customer) {
			if ($dtoDocument->customer->guid !== null) {
				$customer = $this->customerManager->findByShoptetGuid($project, $dtoDocument->customer->guid);
				if (!$customer instanceof Customer) {
					$customer = $this->customerManager->synchronizeFromShoptet($project, $dtoDocument->customer->guid);
				}
			}

			$document->setEmail($dtoDocument->customer->email);
			$document->setPhone($dtoDocument->customer->phone);
			$document->setTaxId($dtoDocument->customer->vatId);
			$document->setVatId($dtoDocument->customer->vatId);
			$document->setCompanyId($dtoDocument->customer->companyId);
			if ($dtoDocument->customer->guid === null) {
				$customer = $this->customerMapping->mapByDocument($document);
			}
		}
		if (!$customer instanceof Customer) {
			$customer = $this->customerManager->getEndUser($project);
		}
		$document->setCustomer($customer);
	}

	protected function fillBasicData(Document $document, DTODocument $dtoDocument): void
	{
		$document->setCode($dtoDocument->code);
		$document->setShoptetCode($dtoDocument->code);
		$document->setOrderCode($dtoDocument->orderCode);

		if ($dtoDocument->orderCode !== null) {
			$existsOrder = $this->orderManager->findByShoptet($document->getProject(), $dtoDocument->orderCode);
			if ($existsOrder instanceof Order) {
				$document->setOrder($existsOrder);
			} else {
				$document->setOrder(null);
			}
		} else {
			$document->setOrder(null);
		}

		$document->setAddressesEqual($dtoDocument->addressesEqual);
		$document->setIsValid($dtoDocument->isValid);
		$document->setVarSymbol((string)$dtoDocument->varSymbol);
		$document->setCreationTime($dtoDocument->creationTime);
		$document->setChangeTime($dtoDocument->changeTime);
		$document->setDueDate($dtoDocument->dueDate);
		$document->setIssueDate($dtoDocument->creationTime);
		if ($dtoDocument->billingMethod instanceof BillingMethod) {
			$document->setBillingMethodId($dtoDocument->billingMethod->id);
			$document->setBillingMethod(
				$this->billingMethodMapper->getBillingMethod($dtoDocument->billingMethod->id)
			);
		} else {
			$document->setBillingMethodId(null);
			$document->setBillingMethod(null);
		}
		$document->setVat((float)$dtoDocument->price->vat);
		$document->setVatRate((int)$dtoDocument->price->vatRate);
		$document->setToPay((float)$dtoDocument->price->toPay);
		$document->setCurrencyCode($dtoDocument->price->currencyCode);
		$document->setCurrency(
			$this->currencyManager->getByCurrency(
				$document->getProject(),
				$document->getCurrencyCode(),
				$document->getOrder() instanceof Order && $document->getOrder()->isCashDeskOrder()
			)
		);
		$document->setWithVat((float)$dtoDocument->price->withVat);
		$document->setWithoutVat((float)$dtoDocument->price->withoutVat);
		$document->setExchangeRate((float)$dtoDocument->price->exchangeRate);


		try {
			$exchangeRate = (float)$dtoDocument->price->exchangeRate;
			if ($exchangeRate > 0.0 && $document->getWithoutVat() !== null && $document->getWithoutVat() > 0.0) {
				$scale = 5;

				$priceWithoutVat = BigDecimal::of($document->getWithoutVat())->toScale($scale, $this->getRoundingMode($document->getCurrency()));
				$orderExchangeRate = BigDecimal::of($exchangeRate)->toScale($scale, $this->getRoundingMode($document->getCurrency()));
				$temp = $priceWithoutVat->dividedBy($orderExchangeRate, $scale, $this->getRoundingMode($document->getCurrency()));
				$finaleExchangeRate = $temp->dividedBy($priceWithoutVat, $scale, $this->getRoundingMode($document->getCurrency()));
				$document->setExchangeRate($finaleExchangeRate->toFloat());

				$withoutVat = BigDecimal::of($document->getWithoutVat())->toScale($scale, $this->getRoundingMode($document->getCurrency()));
				$withVat = BigDecimal::of($document->getWithVat())->toScale($scale, $this->getRoundingMode($document->getCurrency()));
				$toPay = BigDecimal::of($document->getToPay())->toScale($scale, $this->getRoundingMode($document->getCurrency()));
				$document->setMainWithoutVat(
					$withoutVat->multipliedBy($finaleExchangeRate)
						->toScale($scale, $this->getRoundingMode($document->getCurrency()))
						->toFloat()
				);
				$document->setMainWithVat(
					$withVat->multipliedBy($finaleExchangeRate)
						->toScale($scale, $this->getRoundingMode($document->getCurrency()))
						->toFloat()
				);

				$document->setMainToPay(
					$toPay->multipliedBy($finaleExchangeRate)
						->toScale($scale, $this->getRoundingMode($document->getCurrency()))
						->toFloat()
				);
			}
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			throw $exception;
		}

		$document->setEshopBankAccount($dtoDocument->eshop->bankAccount);
		$document->setEshopIban($dtoDocument->eshop->iban);
		$document->setEshopBic($dtoDocument->eshop->bic);
		$document->setEshopTaxMode($dtoDocument->eshop->taxMode);
		$document->setEshopDocumentRemark($dtoDocument->documentRemark);
		$document->setVatPayer($dtoDocument->vatPayer);
		$document->setWeight((float)$dtoDocument->weight);
		$document->setCompletePackageWeight((float)$dtoDocument->completePackageWeight);
	}
}
