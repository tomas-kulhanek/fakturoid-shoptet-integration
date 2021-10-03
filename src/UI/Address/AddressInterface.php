<?php

namespace App\UI\Address;

interface AddressInterface
{
	public function getFullName(): ?string;

	public function getCompany(): ?string;

	public function getStreet(): ?string;

	public function getCity(): ?string;

	public function getDistrict(): ?string;

	public function getZip(): ?string;

	public function getCountryCode(): ?string;

	public function getRegionName(): ?string;

	public function getRegionShortcut(): ?string;

	public function getHouseNumber(): ?string;
}
