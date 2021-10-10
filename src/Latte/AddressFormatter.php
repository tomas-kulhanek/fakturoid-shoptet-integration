<?php

declare(strict_types=1);


namespace App\Latte;

use App\UI\Address\AddressInterface;
use Nette\Utils\Html;

class AddressFormatter
{
	public function __construct(
		private \App\Formatter\AddressFormatter $addressFormatter
	)
	{
	}

	public function __invoke(?AddressInterface $originalAddress, bool $html = true, ?string $registrationNo = null, ?string $vatId = null): Html
	{
		return Html::fromHtml($this->addressFormatter->format($originalAddress, $html, $registrationNo, $vatId));
	}
}
