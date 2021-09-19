<?php

declare(strict_types=1);

namespace App\Utils\File\Restriction;

use App\Database\Entity\File;

class DenyType implements IType
{
	public const TYPE_NAME = 'deny';

	public function canView(File $fileEntity): bool
	{
		return false;
	}

	public function getIdentifier(): string
	{
		return self::TYPE_NAME;
	}
}
