<?php

declare(strict_types=1);

namespace App\Utils\FileUploadFilter;

use App\Database\Entity\File;
use Nette\Forms\Controls\UploadControl;
use Nette\Http\FileUpload;
use Nette\Utils\Arrays;

abstract class BaseFilter implements IMimeTypeFilter
{
	/**
	 * @return array<string,string>
	 */
	abstract protected function getMimeTypes(): array;

	public function checkType(UploadControl $uploadControl): bool
	{
		$files = $uploadControl->getValue();
		if ($files instanceof FileUpload) {
			$files = [$files];
		}
		foreach ($files as $file) {
			if (Arrays::searchKey($this->getMimeTypes(), $file->getContentType()) !== null) {
				continue;
			} else {
				// Pokud se nepodaří ověřit mimetype, ověříme alespoň koncovku.
				if (array_search($this->getExtension($file->getName()), array_unique($this->getMimeTypes())) !== false) {
					continue;
				}
			}
			return false;
		}
		return true;
	}

	public function checkTypeFromFile(File $File): bool
	{
		if (Arrays::searchKey($this->getMimeTypes(), $File->getMimeType()) !== null) {
			return true;
		} else {
			// Pokud se nepodaří ověřit mimetype, ověříme alespoň koncovku.
			if (array_search($this->getExtension($File->getName()), array_unique($this->getMimeTypes())) !== false) {
				return true;
			}
		}
		return false;
	}

	public function getAllowedTypes(): string
	{
		return implode(', ', array_unique($this->getMimeTypes()));
	}

	private function getExtension(string $filename): string
	{
		$exploded = explode('.', $filename);

		return $exploded[count($exploded) - 1];
	}
}
