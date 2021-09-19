<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class HtmlFilter extends BaseFilter
{
	/**
	 * @return array<string, string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'text/plain' => 'txt',
			'text/latte' => 'latte',
			'text/html' => 'html',
			'text/htm' => 'htm',
		];
	}
}
