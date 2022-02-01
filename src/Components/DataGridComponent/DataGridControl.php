<?php

declare(strict_types=1);

namespace App\Components\DataGridComponent;

use Nette\ComponentModel\IContainer;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\Exception\DataGridException;
use Ublaboo\DataGrid\Filter\FilterDate;
use Ublaboo\DataGrid\Filter\FilterDateRange;
use Ublaboo\DataGrid\Filter\FilterMultiSelect;
use Ublaboo\DataGrid\Filter\FilterRange;
use Ublaboo\DataGrid\Filter\FilterSelect;
use Ublaboo\DataGrid\Filter\FilterText;

class DataGridControl extends DataGrid
{
	public function __construct(?IContainer $parent = null, ?string $name = null)
	{
		parent::__construct($parent, $name);
		$this->setItemsPerPageList([10, 25, 50, 100, 200, 500], false);
	}

	public function setExportable(): void
	{
		$this->addExportCsv('messages.grid.button.completeCsvExport', 'completeExport.csv', 'utf-8', ';', true);
		$this->addExportCsv('messages.grid.button.filteredCsvExport', 'completeExport.csv', 'utf-8', ';', true, true);
	}

	public function cantSetHiddenColumn(string $columnName): void
	{
		if (isset($this->columnsVisibility[$columnName])) {
			unset($this->columnsVisibility[$columnName]);
		}
	}


	/**
	 * @param array|string $columns
	 */
	public function addFilterText(
		string $key,
		string $name,
		$columns = null
	): FilterText {
		$filterRange = parent::addFilterText($key, $name, $columns);
		$filterRange->setTemplate(__DIR__ . '/templates/datagrid_filter_text.latte');
		return $filterRange;
	}


	public function addFilterSelect(
		string  $key,
		string  $name,
		array   $options,
		?string $column = null
	): FilterSelect {
		$filterRange = parent::addFilterSelect($key, $name, $options, $column);
		$filterRange->setTemplate(__DIR__ . '/templates/datagrid_filter_select.latte');
		return $filterRange;
	}


	public function addFilterMultiSelect(
		string  $key,
		string  $name,
		array   $options,
		?string $column = null
	): FilterMultiSelect {
		$filterRange = parent::addFilterMultiSelect($key, $name, $options, $column);
		$filterRange->setTemplate(__DIR__ . '/templates/datagrid_filter_select.latte');
		return $filterRange;
	}


	public function addFilterDate(string $key, string $name, ?string $column = null): FilterDate
	{
		$filterRange = parent::addFilterDate($key, $name, $column);
		$filterRange->setTemplate(__DIR__ . '/templates/datagrid_filter_date.latte.latte');
		return $filterRange;
	}


	public function addFilterRange(
		string  $key,
		string  $name,
		?string $column = null,
		string  $nameSecond = '-'
	): FilterRange {
		$filterRange = parent::addFilterRange($key, $name, $column, $nameSecond);
		$filterRange->setTemplate(__DIR__ . '/templates/datagrid_filter_range.latte');
		return $filterRange;
	}


	/**
	 * @throws DataGridException
	 */
	public function addFilterDateRange(
		string  $key,
		string  $name,
		?string $column = null,
		string  $nameSecond = '-'
	): FilterDateRange {
		$filterRange = parent::addFilterDateRange($key, $name, $column, $nameSecond);
		$filterRange->setTemplate(__DIR__ . '/templates/datagrid_filter_daterange.latte');
		return $filterRange;
	}
}
