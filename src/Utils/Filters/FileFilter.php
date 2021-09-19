<?php

declare(strict_types=1);

namespace Troid\CoreModule\Filters;

use App\Database\Entity\File;
use App\Utils\FileUploadFilter\ArchiveFilter;
use App\Utils\FileUploadFilter\AudioFilter;
use App\Utils\FileUploadFilter\ExcelDocumentFilter;
use App\Utils\FileUploadFilter\HtmlFilter;
use App\Utils\FileUploadFilter\ImageFilter;
use App\Utils\FileUploadFilter\PdfDocumentFilter;
use App\Utils\FileUploadFilter\PowerPointDocumentFilter;
use App\Utils\FileUploadFilter\WordDocumentFilter;

class FileFilter
{
	public const PATHINFO_EXTENSION = PATHINFO_EXTENSION;

	/**
	 * @param File $File
	 * @param int|string $param
	 * @return string
	 */
	public function __invoke(File $File, $param = self::PATHINFO_EXTENSION): string
	{
		if ($param === 'icon') {
			return $this->getFileIcon($File);
		}
		/** @var int $param */
		return pathinfo($File->getName(), $param);
	}

	private function getFileIcon(File $File): string
	{
		if ((new WordDocumentFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-word';
		}
		if ((new ExcelDocumentFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-excel';
		}
		if ((new PowerPointDocumentFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-powerpoint';
		}
		if ((new ImageFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-image';
		}
		if ((new AudioFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-audio';
		}
		if ((new ArchiveFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-archive';
		}
		if ((new PdfDocumentFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-pdf';
		}
		if ((new HtmlFilter())->checkTypeFromFile($File)) {
			return 'fas fa-file-code';
		}
		return 'fas fa-file-alt';
	}
}
