<?php

namespace App\Exception;

use Fakturoid\Exception;

class FakturoidException extends \Exception
{
	public static function createFromLibraryExcpetion(Exception $exception): FakturoidException
	{
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

		return json_decode($this->getMessage(), true);
	}

	/**
	 * @return array<string, string[]>
	 */
	public function getErrors(): array
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
			$rows[] = $column . ' - ' . join(' ', $errors);
		}

		return join(PHP_EOL, $rows);
	}
}
