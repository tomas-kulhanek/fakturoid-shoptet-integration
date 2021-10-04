<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class OrderStatuses
{
	/** @var ArrayCollection<int, OrderStatus>|Collection<int, OrderStatus> */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'ArrayCollection<int, EshopInfoErrorResponse>')]
	#[Serializer\Type(name: 'ArrayCollection<App\DTO\Shoptet\EshopInfo\OrderStatus>')]
	#[Serializer\Accessor(getter: 'setStatuses', setter: 'setStatuses')]
	public ArrayCollection|Collection $statuses;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'integer')]
	public int $defaultStatus;

	public function __construct()
	{
		$this->statuses = new ArrayCollection();
	}

	/**
	 * @param ArrayCollection<int, OrderStatus>|Collection<int, OrderStatus>|null $statuses
	 */
	public function setStatuses(null|ArrayCollection|Collection $statuses): void
	{
		if (!$statuses instanceof Collection) {
			$statuses = new ArrayCollection();
		}
		/** @var ArrayCollection<int, OrderStatus>|Collection<int, OrderStatus> $statuses */
		$this->statuses = $statuses;
	}

	/**
	 * @return ArrayCollection<int, OrderStatus>|Collection<int, OrderStatus>
	 */
	public function getStatuses(): ArrayCollection|Collection
	{
		return $this->statuses;
	}
}
