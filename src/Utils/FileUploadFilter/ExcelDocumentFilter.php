<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class ExcelDocumentFilter extends BaseFilter
{
	/**
	 * @return array<string,string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'application/vnd.ms-excel' => '.xlt',
			'application/vnd.ms-excel-xla' => '.xla',
			'application/vnd.ms-excel-xls' => '.xls',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => '.xltx',
			'application/vnd.ms-excel.sheet.macroEnabled.12' => '.xlsm',
			'application/vnd.ms-excel.template.macroEnabled.12' => '.xltm',
			'application/vnd.ms-excel.addin.macroEnabled.12' => '.xlam',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12' => '.xlsb',
		];
	}
}
