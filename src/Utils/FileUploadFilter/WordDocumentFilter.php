<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class WordDocumentFilter extends BaseFilter
{
	/**
	 * @return array<string,string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'application/msword' => '.doc',
			'application/msword-dot' => '.dot',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => '.docx',
			'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => '.dotx',
			'application/vnd.ms-word.document.macroEnabled.12' => '.docm',
			'application/vnd.ms-word.template.macroEnabled.12' => '.dotm',
		];
	}
}
