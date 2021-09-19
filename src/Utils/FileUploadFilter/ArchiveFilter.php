<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class ArchiveFilter extends BaseFilter
{
	/**
	 * @return array<string,string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'application/zip' => 'zip',
			'application/x-rar-compressed' => 'rar',
			'application/x-tar' => 'tar',
			'application/x-7z-compressed' => '7z',
		];
	}
}
