<?php

declare(strict_types=1);

namespace App\Monolog\Slack\Formatter;

use App\Exception\Runtime\InvalidStateException;
use Throwable;
use Tracy\ILogger;

final class ColorFormatter implements IFormatter
{
	public function format(SlackContext $context, Throwable $message, string $priority): SlackContext
	{
		switch ($priority) {
			case ILogger::ERROR:
				$color = 'warning';

				break;
			case ILogger::EXCEPTION:
				$color = '#ff0000';

				break;
			case ILogger::CRITICAL:
				$color = 'danger';

				break;
			default:
				throw new InvalidStateException(sprintf('Unsupported priority "%s".', $priority));
		}

		$context = clone $context;
		$context->setColor($color);

		return $context;
	}
}
