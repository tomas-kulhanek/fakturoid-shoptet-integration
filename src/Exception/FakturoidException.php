<?php

namespace App\Exception;

use Fakturoid\Exception;
use Tracy\Debugger;
use Tracy\ILogger;

class FakturoidException extends \Exception
{
	public static function createFromLibraryExcpetion(Exception $exception): FakturoidException
	{
		Debugger::log($exception);
		Debugger::log($exception->getMessage(), ILogger::CRITICAL);
		return new self($exception->getMessage(), $exception->getCode(), $exception);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function getParsedMessage(): array
	{
		if (trim($this->getMessage()) === '') {
			return [];
		}

		return json_decode($this->getMessage(), true, 512, JSON_THROW_ON_ERROR);
	}

	public function getErrors(): mixed
	{
		if (key_exists('errors', $this->getParsedMessage())) {
			return $this->getParsedMessage()['errors'];
		}

		return [];
	}

	public function humanize(): string
	{
		$rows = [];
		foreach ($this->getErrors() as $column => $errors) {
			if (is_array($errors)) {
				$rows[] = sprintf('%s - %s', $column, join(' ', $errors));
			} elseif (is_string($errors)) {
				$rows[] = sprintf('%s - %s', $column, $errors);
			}
		}

		return join(PHP_EOL, $rows);
	}
}
