<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\UI\Address\AddressInterface;
use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: OrderDeliveryAddress::class)]
#[ORM\Table(name: 'sf_order_delivery_address')]
#[ORM\HasLifecycleCallbacks]
class OrderDeliveryAddress implements AddressInterface
{
	use Attributes\TId;

	#[ORM\OneToOne(targetEntity: Order::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected Order $document;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $company = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $fullName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $street = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $houseNumber = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $city = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $district = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $additional = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $zip = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $countryCode = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $regionName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $regionShortcut = null;


	public function getDocument(): Order
	{
		return $this->document;
	}

	public function setDocument(Order $document): void
	{
		$this->document = $document;
	}

	public function setCompany(?string $company): void
	{
		$this->company = $company;
	}

	public function setFullName(?string $fullName): void
	{
		$this->fullName = $fullName;
	}

	public function setStreet(?string $street): void
	{
		$this->street = $street;
	}

	public function setHouseNumber(?string $houseNumber): void
	{
		$this->houseNumber = $houseNumber;
	}

	public function setCity(?string $city): void
	{
		$this->city = $city;
	}

	public function setDistrict(?string $district): void
	{
		$this->district = $district;
	}

	public function setAdditional(?string $additional): void
	{
		$this->additional = $additional;
	}

	public function setZip(?string $zip): void
	{
		$this->zip = $zip;
	}

	public function setCountryCode(?string $countryCode): void
	{
		$this->countryCode = $countryCode;
	}

	public function setRegionName(?string $regionName): void
	{
		$this->regionName = $regionName;
	}

	public function setRegionShortcut(?string $regionShortcut): void
	{
		$this->regionShortcut = $regionShortcut;
	}

	public function getCompany(): ?string
	{
		return $this->company;
	}

	public function getFullName(): ?string
	{
		return $this->fullName;
	}

	public function getStreet(): ?string
	{
		return $this->street;
	}

	public function getHouseNumber(): ?string
	{
		return $this->houseNumber;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	public function getDistrict(): ?string
	{
		return $this->district;
	}

	public function getAdditional(): ?string
	{
		return $this->additional;
	}

	public function getZip(): ?string
	{
		return $this->zip;
	}

	public function getCountryCode(): ?string
	{
		return $this->countryCode;
	}

	public function getRegionName(): ?string
	{
		return $this->regionName;
	}

	public function getRegionShortcut(): ?string
	{
		return $this->regionShortcut;
	}
}
