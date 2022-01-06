<?php

declare(strict_types=1);


namespace App\Monolog;

class TracyAws
{
	public static function getPrefixedPath(string $basePrefix): string
	{
		return sprintf(
			'%s/%s/',
			rtrim($basePrefix, '/'),
			date('Y/m/d')
		);
	}
}
