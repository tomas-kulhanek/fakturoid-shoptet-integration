<?php

declare(strict_types=1);

namespace App\Monolog\Slack\Formatter;

use Throwable;

interface IFormatter
{
	public function format(SlackContext $context, Throwable $message, string $priority): SlackContext;
}
