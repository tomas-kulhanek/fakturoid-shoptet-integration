<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class EshopInfoResponse
{
	//public ?object $contactInformation = null;
	//public ?object $billingInformation = null;
	//public ?object $settings = null;
	//public ?object $currencies = null;
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
}
