<?php

declare(strict_types=1);


namespace App\Latte;

use Nette\Utils\Html;

class ProductTypeIcon
{
	public function __invoke(string $productType): Html
	{
		$html = Html::el('i');

		switch ($productType) {
			case 'product':
				$html->class('fab fa-product-hunt');
				break;
			case 'bazar':
				$html->class('fas fa-concierge-bell');
				break;
			case 'service':
				$html->class('');
				break;
			case 'shipping':
				$html->class('fas fa-truck');
				break;
			case 'billing':
				$html->class('fas fa-coins');
				break;
			case 'discount-coupon':
				$html->class('fas fa-tags');
				break;
			case 'volume-discount':
				$html->class('fas fa-percent');
				break;
			case 'gift':
				$html->class('fas fa-gift');
				break;
			case 'gift-certificate':
				$html->class('fas fa-certificate');
				break;
			case 'generic-item':
				$html->class('');
				break;
			case 'product-set':
				$html->class('fas fa-toolbox');
				break;
			default:
				$html->class('');
		}

		return $html->title($productType);
	}
}
