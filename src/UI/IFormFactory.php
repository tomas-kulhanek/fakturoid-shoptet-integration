<?php

declare(strict_types=1);

namespace App\UI;

interface IFormFactory extends \Contributte\FormWizard\IFormFactory
{
	public function create(bool $csrfProtection = false): Form;
}
