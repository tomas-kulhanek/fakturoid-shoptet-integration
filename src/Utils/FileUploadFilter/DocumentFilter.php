<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class DocumentFilter extends BaseFilter
{
	/**
	 * @return array<string, string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'text/plain' => 'txt',
			'application/msword' => 'doc',
			'application/vnd.ms-excel' => 'xls',
			'application/pdf' => 'pdf',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
			'application/vnd.ms-powerpoint' => 'ppt',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
		];
	}
}
