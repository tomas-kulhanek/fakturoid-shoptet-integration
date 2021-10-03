<?php

declare(strict_types=1);

namespace App\Monolog\Slack\Formatter;

use Throwable;

final class ExceptionStackTraceFormatter implements IFormatter
{
	public function format(SlackContext $context, Throwable $exception, string $priority): SlackContext
	{
		// Skip empty trace
		if (count($exception->getTrace()) < 1) {
			return $context;
		}

		$context = clone $context;
		$attachment = $context->createAttachment();
		$attachment->setText(sprintf('*Stack trace* (_%s_)', get_class($exception)));
		$attachment->setMarkdown();
		foreach ($exception->getTrace() as $id => $trace) {
			$func = $attachment->createField();
			$func->setTitle(sprintf(':fireworks: Trace #%s', $id + 1));
			$file = $attachment->createField();
			$file->setTitle(':open_file_folder: File');
			$file->setValue('```Function: ' . $trace['function'] . "\nFile: " . ($trace['file'] ?? '') . ':' . ($trace['line'] ?? '') . '```');
		}

		return $context;
	}
}
