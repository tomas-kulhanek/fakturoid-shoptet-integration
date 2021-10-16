<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class EshopInfoResponse
{
	//public ?object $contactInformation = null;
	//public ?object $billingInformation = null;
	//public ?object $settings = null;
	//public ?object $currencies = null;

	/** @var ArrayCollection<int, Currency>|Collection<int, Currency> */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'ArrayCollection<int, Currency>')]
	#[Serializer\Type(name: 'ArrayCollection<App\DTO\Shoptet\EshopInfo\Currency>')]
	#[Serializer\Accessor(getter: 'getCurrencies', setter: 'setCurrencies')]
	public ArrayCollection|Collection $currencies;
	//public ?object $taxClasses = null;
	//public ?object $urls = null;
	//public ?object $socialNetworks = null;
	//public ?object $orderAdditionalFields = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: OrderStatuses::class)]
	public ?OrderStatuses $orderStatuses = null;

	//public ?object $shippingMethods = null;
	//public ?object $paymentMethods = null;
	//public ?object $imageCuts = null;
	#[Assert\Type(type: 'boolean')]
	public ?bool $trial = false;


	/**
	 * @param ArrayCollection<int, Currency>|Collection<int, Currency>|null $currencies
	 */
	public function setCurrencies(null|ArrayCollection|Collection $currencies): void
	{
		if (!$currencies instanceof Collection) {
			$currencies = new ArrayCollection();
		}
		/** @var ArrayCollection<int, Currency>|Collection<int, Currency> $currencies */
		$this->currencies = $currencies;
	}

	/**
	 * @return ArrayCollection<int, Currency>|Collection<int, Currency>
	 */
	public function getCurrencies(): ArrayCollection|Collection
	{
		return $this->currencies;
	}
}
