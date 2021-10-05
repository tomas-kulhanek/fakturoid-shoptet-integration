<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class CustomerAddress
{
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

	public function getCompany(): ?string
	{
		return $this->company;
	}

	public function setCompany(?string $company): void
	{
		$this->company = $company;
	}

	public function getFullName(): ?string
	{
		return $this->fullName;
	}

	public function setFullName(?string $fullName): void
	{
		$this->fullName = $fullName;
	}

	public function getStreet(): ?string
	{
		return $this->street;
	}

	public function setStreet(?string $street): void
	{
		$this->street = $street;
	}

	public function getHouseNumber(): ?string
	{
		return $this->houseNumber;
	}

	public function setHouseNumber(?string $houseNumber): void
	{
		$this->houseNumber = $houseNumber;
	}

	public function getCity(): ?string
	{
		return $this->city;
	}

	public function setCity(?string $city): void
	{
		$this->city = $city;
	}

	public function getDistrict(): ?string
	{
		return $this->district;
	}

	public function setDistrict(?string $district): void
	{
		$this->district = $district;
	}

	public function getAdditional(): ?string
	{
		return $this->additional;
	}

	public function setAdditional(?string $additional): void
	{
		$this->additional = $additional;
	}

	public function getZip(): ?string
	{
		return $this->zip;
	}

	public function setZip(?string $zip): void
	{
		$this->zip = $zip;
	}

	public function getCountryCode(): ?string
	{
		return $this->countryCode;
	}

	public function setCountryCode(?string $countryCode): void
	{
		$this->countryCode = $countryCode;
	}

	public function getRegionName(): ?string
	{
		return $this->regionName;
	}

	public function setRegionName(?string $regionName): void
	{
		$this->regionName = $regionName;
	}

	public function getRegionShortcut(): ?string
	{
		return $this->regionShortcut;
	}

	public function setRegionShortcut(?string $regionShortcut): void
	{
		$this->regionShortcut = $regionShortcut;
	}
}
