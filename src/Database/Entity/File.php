<?php

declare(strict_types=1);


namespace App\Database\Entity;

use App\Database\Entity\Attributes\TCreatedAt;
use App\Database\Entity\Attributes\TGuid;
use App\Database\Entity\Attributes\TId;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Database\Repository\FileRepository")
 * @ORM\Table(name="`file`")
 * @ORM\HasLifecycleCallbacks
 */
class File extends AbstractEntity
{
	use TCreatedAt;
	use TId;
	use TGuid;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	protected string $name;

	/**
	 * @ORM\Column(type="integer", nullable=false)
	 */
	protected int $size;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	protected string $mimeType;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	protected string $path;

	/**
	 * @ORM\Column(type="string", length=100, options={"default" : "deny"})
	 */
	protected string $restrictionType = 'deny';

	public function __construct(string $name, string $path, int $size, string $mimeType, string $restrictionType)
	{
		$this->name = $name;
		$this->path = $path;
		$this->size = $size;
		$this->mimeType = $mimeType;
		$this->restrictionType = $restrictionType;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getExtension(): string
	{
		return pathinfo($this->getName(), PATHINFO_EXTENSION);
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function getMimeType(): string
	{
		return $this->mimeType;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function getRestrictionType(): string
	{
		return $this->restrictionType;
	}
}
