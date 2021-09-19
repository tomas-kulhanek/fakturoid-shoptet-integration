<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

class PowerPointDocumentFilter extends BaseFilter
{
	/**
	 * @return array<string,string>
	 */
	protected function getMimeTypes(): array
	{
		return [
			'application/vnd.ms-powerpoint' => '.ppt',
			'application/vnd.ms-powerpoint-pot' => '.pot',
			'application/vnd.ms-powerpoint-pps' => '.pps',
			'application/vnd.ms-powerpoint-ppa' => '.ppa',
			'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx',
			'application/vnd.openxmlformats-officedocument.presentationml.template' => '.potx',
			'application/vnd.openxmlformats-officedocument.presentationml.slideshow' => '.ppsx',
			'application/vnd.ms-powerpoint.addin.macroEnabled.12' => '.ppam',
			'application/vnd.ms-powerpoint.presentation.macroEnabled.12' => '.pptm',
			'application/vnd.ms-powerpoint.template.macroEnabled.12' => '.potm',
			'application/vnd.ms-powerpoint.slideshow.macroEnabled.12' => '.ppsm',
		];
	}
}
