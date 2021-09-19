<?php

declare(strict_types=1);

namespace App\Utils\File;

use App\Database\Entity\File;
use App\Database\EntityManager;
use App\Exception\Logic\FileForbiddenException;
use App\Exception\Runtime\FileUploadException;
use App\Security\SecurityUser;
use App\Utils\File\Restriction\DenyType;
use App\Utils\File\Restriction\RestrictionChecker;
use Doctrine\DBAL\Exception;
use League\Flysystem\FilesystemInterface;
use Nette\Http\FileUpload;
use Ramsey\Uuid\UuidInterface;
use Tracy\Debugger;

class FileManager
{
	public function __construct(
		private FilesystemInterface $filesystem,
		private EntityManager $em,
		private SecurityUser $userHelper,
		private RestrictionChecker $fileRestrictionChecker
	) {
	}

	public function saveUnknownFile(string $fileName, string $tempPath, string $restrictionType = DenyType::TYPE_NAME): File
	{
		return $this->saveFile($tempPath, $fileName, (int) filesize($tempPath), 'application/octet-stream', $restrictionType);
	}

	private function saveFileToStorage(string $tempPath): string
	{
		$filePath = $this->getInternalFilePath($tempPath);
		if (!$this->filesystem->has($filePath)) {
			$stream = fopen($tempPath, 'r+');
			if (!is_resource($stream) || !$this->filesystem->writeStream($filePath, $stream)) {
				throw new FileUploadException();
			}
		}
		return $filePath;
	}

	public function saveFileUpload(FileUpload $fileUpload, string $restrictionType = DenyType::TYPE_NAME): File
	{
		return $this->saveFile(
			$fileUpload->getTemporaryFile(),
			$fileUpload->getName(),
			$fileUpload->getSize(),
			$fileUpload->getContentType(),
			$restrictionType
		);
	}

	public function saveFile(string $filePath, string $name, int $size, string $contentType, string $restrictionType = DenyType::TYPE_NAME): File
	{
		$filePath = $this->saveFileToStorage($filePath);
		$File = new File(
			$name,
			$filePath,
			$size,
			$contentType,
			$restrictionType
		);
		try {
			$this->em->persist($File);
			$this->em->flush($File);
		} catch (Exception $exception) {
			$this->filesystem->delete($filePath);
			Debugger::log($exception);
			throw new FileUploadException('', 0, $exception);
		}

		return $File;
	}

	public function getFileResponse(string $uuid): FileResponse
	{
		/** @var File $File */
		$File = $this->em->getFileRepository()->findOneBy(['uuid' => $uuid]);
		if (!$this->fileRestrictionChecker->check($File)) {
			throw new FileForbiddenException();
		}
		return new FileResponse($File, $this->filesystem);
	}


	private function getInternalFilePath(string $filePath): string
	{
		$fileHash = (string) sha1_file($filePath);
		$splittedFileHash = [
			date('Ym'),
			substr($fileHash, 0, 3),
			substr($fileHash, 3),
		];
		return implode(DIRECTORY_SEPARATOR, $splittedFileHash);
	}

	/**
	 * @param File $File
	 * @return string|false
	 * @throws \League\Flysystem\FileNotFoundException
	 */
	public function readFile(File $File)
	{
		return $this->filesystem->read($File->getPath());
	}

	public function getFile(UuidInterface $uuid): File
	{
		/** @var File $File */
		$File = $this->em->getFileRepository()->findOneBy(['uuid' => $uuid]);
		return $File;
	}

	public function deleteFile(File $File): void
	{
		$this->removeFile($File);
	}

	private function removeFile(File $File): void
	{
		$result = $this->em->getFileRepository()->findBy(['path' => $File->getPath()]);
		if (count($result) === 1) {
			$this->filesystem->delete($File->getPath());
		}
		$this->em->remove($File);
	}
}
