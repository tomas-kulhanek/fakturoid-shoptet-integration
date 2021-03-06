<?php

declare(strict_types=1);

namespace App\Components\DataGridComponent;

use Nette\ComponentModel\IContainer;

class DataGridFactory
{
	public function __construct(private \Nette\Localization\Translator $translator)
	{
	}

	public function create(bool $columnsHideable = true, bool $filters = true, ?IContainer $parent = null, ?string $name = null): DataGridControl
	{
		$control = new DataGridControl($parent, $name);

		$control->setTranslator($this->translator);
		$control->setDefaultPerPage(50);
		$control->setTemplateFile(__DIR__ . '/templates/datagrid.latte');
		if ($filters) {
			$control->setOuterFilterRendering();
		}
		if ($columnsHideable) {
			$control->setColumnsHideable();
		}
		return $control;
	}
}
