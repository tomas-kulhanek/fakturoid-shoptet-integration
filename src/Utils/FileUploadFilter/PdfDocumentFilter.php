<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class PdfDocumentFilter extends BaseFilter
{
	/**
	 * @return array<string,string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'application/pdf' => '.pdf',
		];
	}
}
