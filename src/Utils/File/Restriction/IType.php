<?php

declare(strict_types=1);

namespace App\Utils\File\Restriction;

use App\Database\Entity\File;

interface IType
{
	public function canView(File $fileEntity): bool;

	public function getIdentifier(): string;
}
