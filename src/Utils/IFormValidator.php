<?php

declare(strict_types=1);

namespace App\Utils;

use Nette\Forms\Control;

interface IFormValidator
{
	public function validateIco(Control $control): bool;

	public function validateUrl(Control $control): bool;

	public function validateRc(Control $control): bool;

	public function validateVatNumber(Control $control, bool $params = false): bool;
}
