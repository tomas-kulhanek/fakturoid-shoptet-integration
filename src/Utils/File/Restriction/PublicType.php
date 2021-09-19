<?php

declare(strict_types=1);

namespace App\Utils\File\Restriction;

use App\Database\Entity\File;

class PublicType implements IType
{
	public const TYPE_NAME = 'public';

	public function canView(File $fileEntity): bool
	{
		return true;
	}

	public function getIdentifier(): string
	{
		return self::TYPE_NAME;
	}
}
