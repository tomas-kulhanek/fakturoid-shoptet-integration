<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class AudioFilter extends BaseFilter
{
	/**
	 * @return array<string,string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'audio/mpeg3' => 'mp3',
			'audio/x-mpeg-3' => 'mp3',
			'audio/ogg' => 'ogg',
			'audio/x-aiff' => 'aiff',
		];
	}
}
