<?php

declare(strict_types=1);

namespace App\Components\DataGridComponent;

use Ublaboo\DataGrid\DataGrid;

class DataGridControl extends DataGrid
{
	public function setExportable(): void
	{
		$this->addExportCsv('core.button.completeCsvExport', 'completeExport.csv', 'utf-8', ';', true);
		$this->addExportCsv('core.button.filteredCsvExport', 'completeExport.csv', 'utf-8', ';', true, true);
	}

	public function cantSetHiddenColumn(string $columnName): void
	{
		if (isset($this->columnsVisibility[$columnName])) {
			unset($this->columnsVisibility[$columnName]);
		}
	}
}
