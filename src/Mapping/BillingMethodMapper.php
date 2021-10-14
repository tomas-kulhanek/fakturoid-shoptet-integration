<?php

declare(strict_types=1);


namespace App\Mapping;

class BillingMethodMapper
{
	public const BILLING_METHOD_BANK = 'bank';
	public const BILLING_METHOD_COD = 'cod';
	public const BILLING_METHOD_CASH = 'cash';
	public const BILLING_METHOD_CARD = 'bank';

	public const BILLING_METHODS = [
		self::BILLING_METHOD_BANK,
		self::BILLING_METHOD_COD,
		self::BILLING_METHOD_CASH,
		self::BILLING_METHOD_CARD,
	];

	public function getBillingMethod(?int $billingMethodId): ?string
	{
		return match ($billingMethodId) {
			1 => self::BILLING_METHOD_COD,
			2 => self::BILLING_METHOD_BANK,
			3 => self::BILLING_METHOD_CASH,
			4 => self::BILLING_METHOD_CARD,
			default => null
		};
	}
}
