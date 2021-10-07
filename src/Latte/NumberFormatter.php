<?php

declare(strict_types=1);


namespace App\Latte;

use Contributte\Translation\Translator;

class NumberFormatter
{
	private \NumberFormatter $numberFormatter;

	public function __construct(
		Translator $translator
	) {
		$this->numberFormatter = new \NumberFormatter($translator->getLocale(), \NumberFormatter::CURRENCY);
	}

	public function __invoke(float $price, string $currencyCode = 'CZK'): string
	{
		return $this->numberFormatter->formatCurrency($price, $currencyCode);
	}
}
