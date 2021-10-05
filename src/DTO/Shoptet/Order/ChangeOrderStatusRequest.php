<?php declare(strict_types=1);


namespace App\DTO\Shoptet\Order;


use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class ChangeOrderStatusRequest
{

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type(name: 'boolean')]
	#[Assert\Type(type: 'boolean')]
	public ?bool $paid = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type(name: 'integer')]
	#[Assert\Type(type: 'integer')]
	public ?int $statusId = null;

	#[Serializer\SkipWhenEmpty]
	#[Serializer\Type(name: 'integer')]
	#[Assert\Type(type: 'integer')]
	public ?int $billingMethodId = null;
}
