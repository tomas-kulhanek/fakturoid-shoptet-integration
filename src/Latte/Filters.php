<?php

declare(strict_types=1);

namespace App\Latte;

use Nette\Neon\Neon;
use Nette\StaticClass;
use Nette\Utils\Json;

final class Filters
{
	use StaticClass;

	public static function neon(mixed $value): string
	{
		return Neon::encode($value, Neon::BLOCK);
	}

	public static function json(mixed $value): string
	{
		return Json::encode($value);
	}

	public static function datetime(?\DateTimeInterface $value): string
	{
		return $value->format(\DateTimeInterface::RSS);
	}
}
