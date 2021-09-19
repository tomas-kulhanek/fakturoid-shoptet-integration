<?php

declare(strict_types=1);

namespace App\UI;

use Nette\ComponentModel\IContainer;

interface IFormFactory
{
	public function create(bool $csrfProtection = false, ?IContainer $parent = null, ?string $name = null): Form;
}
