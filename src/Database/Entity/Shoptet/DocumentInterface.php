<?php

namespace App\Database\Entity\Shoptet;

interface DocumentInterface
{
	public function getEmail(): ?string;

	public function getVatId(): ?string;

	public function getCompanyId(): ?string;
}
