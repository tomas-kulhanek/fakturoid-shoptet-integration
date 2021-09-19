<?php

declare(strict_types=1);

namespace App\Utils\File\Restriction;

use App\Database\Entity\File;
use Exception;

class RestrictionChecker
{
	/** @var IType[] */
	private array $restrictionTypes;

	public function addType(IType $type): void
	{
		if (!empty($this->restrictionTypes[$type->getIdentifier()])) {
			throw new Exception();
		}
		$this->restrictionTypes[$type->getIdentifier()] = $type;
	}

	public function getRestrictionType(string $identifier): IType
	{
		if (empty($this->restrictionTypes[$identifier])) {
			throw new Exception();
		}
		return $this->restrictionTypes[$identifier];
	}

	public function check(File $fileEntity): bool
	{
		return $this->getRestrictionType($fileEntity->getRestrictionType())->canView($fileEntity);
	}
}
