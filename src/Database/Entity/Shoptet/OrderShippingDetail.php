<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;

use App\Database\Repository\Shoptet\OrderShippingDetailRepository;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: OrderShippingDetailRepository::class)]
#[ORM\Table(name: 'sf_order_shipping_detail')]
#[ORM\HasLifecycleCallbacks]
class OrderShippingDetail
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Order::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected Order $document;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $branchId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $name = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $note = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $place = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $street;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $city = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $zipCode = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $countryCode = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $link = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $latitude = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $longtitude = null;

	public function setDocument(Order $document): void
	{
		$this->document = $document;
	}

	public function setBranchId(?string $branchId): void
	{
		$this->branchId = $branchId;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function setNote(?string $note): void
	{
		$this->note = $note;
	}

	public function setPlace(?string $place): void
	{
		$this->place = $place;
	}

	public function setStreet(string $street): void
	{
		$this->street = $street;
	}

	public function setCity(?string $city): void
	{
		$this->city = $city;
	}

	public function setZipCode(?string $zipCode): void
	{
		$this->zipCode = $zipCode;
	}

	public function setCountryCode(?string $countryCode): void
	{
		$this->countryCode = $countryCode;
	}

	public function setLink(?string $link): void
	{
		$this->link = $link;
	}

	public function setLatitude(?string $latitude): void
	{
		$this->latitude = $latitude;
	}

	public function setLongtitude(?string $longtitude): void
	{
		$this->longtitude = $longtitude;
	}
}
