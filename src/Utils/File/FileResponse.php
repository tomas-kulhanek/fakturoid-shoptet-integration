<?php

declare(strict_types=1);

namespace App\Utils\File;

use App\Database\Entity\File;
use League\Flysystem\FilesystemInterface;
use Nette;

class FileResponse implements Nette\Application\IResponse
{
	use Nette\SmartObject;

	private bool $forceDownload = true;

	private bool $resuming = true;

	public function __construct(
		private File $File,
		private FilesystemInterface $filesystem
	) {
	}

	public function setForceDownload(bool $forceDownload): void
	{
		$this->forceDownload = $forceDownload;
	}

	public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse): void
	{
		$httpResponse->setContentType($this->File->getMimeType());
		$httpResponse->setHeader(
			'Content-Disposition',
			($this->forceDownload ? 'attachment' : 'inline')
			. '; filename="' . $this->File->getName() . '"'
			. '; filename*=utf-8\'\'' . rawurlencode($this->File->getName())
		);

		$handle = $this->filesystem->readStream($this->File->getPath());
		if (!is_resource($handle)) {
			throw new \Exception('File is not exist.');
		}
		$filesize = $length = $this->File->getSize();
		if ($this->resuming) {
			$httpResponse->setHeader('Accept-Ranges', 'bytes');
			if (preg_match('#^bytes=(\d*)-(\d*)$#D', (string) $httpRequest->getHeader('Range'), $matches)) {
				[, $start, $end] = $matches;
				if ($start === '') {
					$start = max(0, $filesize - $end);
					$end = $filesize - 1;
				} elseif ($end === '' || $end > $filesize - 1) {
					$end = $filesize - 1;
				}
				if ($end < $start) {
					$httpResponse->setCode(416); // requested range not satisfiable
					return;
				}

				$httpResponse->setCode(206);
				$httpResponse->setHeader('Content-Range', 'bytes ' . $start . '-' . $end . '/' . $filesize);
				$length = $end - $start + 1;
				fseek($handle, (int) $start);
			} else {
				$httpResponse->setHeader('Content-Range', 'bytes 0-' . ($filesize - 1) . '/' . $filesize);
			}
		}

		$httpResponse->setHeader('Content-Length', (string) $length);
		while (!feof($handle) && $length > 0) {
			/** @var int $length2 */
			$length2 = min(4_000_000, $length);
			echo $s = fread($handle, $length2);
			if ($s === false) {
				continue;
			}
			$length -= strlen($s);
		}
		fclose($handle);
	}
}
