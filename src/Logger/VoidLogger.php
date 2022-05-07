<?php

declare(strict_types=1);

namespace App\Logger;

use Doctrine\DBAL\Logging\SQLLogger;

class VoidLogger implements SQLLogger
{
	/**
	 * @param string $sql
	 * @param mixed[]|null $params
	 * @param mixed[]|null $types
	 */
	public function startQuery($sql, ?array $params = null, ?array $types = null): void
	{
	}

	public function stopQuery(): void
	{
	}
}
