<?php

declare(strict_types=1);


namespace App\Exception\OAuth;

use App\Exception\LogicException;
use Nette\Http\Url;

class MissingProject extends LogicException
{
	private ?Url $shopUrl = null;

	public function setShopUrl(Url $url): void
	{
		$this->shopUrl = $url;
	}

	public function getShopUrl(): ?Url
	{
		return $this->shopUrl;
	}
}
