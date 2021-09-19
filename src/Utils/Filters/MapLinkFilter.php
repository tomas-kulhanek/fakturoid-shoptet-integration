<?php

declare(strict_types=1);

namespace Troid\CoreModule\Filters;

use Nette\Utils\Html;

class MapLinkFilter
{
	public function __invoke(?string $address): string
	{
		if (empty($address)) {
			return '';
		}
		return (string) Html::el('a', ['href' => 'http://maps.google.com/?q=' . $address, 'target' => '_blank'])->addText($address);
	}
}
