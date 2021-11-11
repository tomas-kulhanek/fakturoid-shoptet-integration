<?php

namespace App\Exception;

use Fakturoid\Exception;

class FakturoidException extends \Exception
{
	public static function createFromLibraryExcpetion(Exception $exception): FakturoidException
	{
		return new self($exception->getMessage(), $exception->getCode(), $exception);
	}

	public function getParsedMessage(): \stdClass
	{
		if (trim($this->getMessage()) !== '') {
			return new \stdClass();
		}
		return json_decode($this->getMessage());
	}

	public function getErrors(): \stdClass
	{
		if (property_exists($this->getParsedMessage(), 'errors')) {
			return $this->getParsedMessage()->errors;
		}
		return new \stdClass();
	}

	public function humanize(): string
	{
		$rows = [];
		foreach ($this->getParsedMessage()->errors as $column => $errors) {
			$rows[] = $column . ' - ' . join(' ', $errors);
		}
		return join(' ', $rows);
	}
}
