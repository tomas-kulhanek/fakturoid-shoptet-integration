<?php

declare(strict_types=1);

namespace App\Security;

use Nette\Security\SimpleIdentity as NetteIdentity;

class Identity extends NetteIdentity
{
	public function getFullName(): string
	{
		return sprintf('%s %s', $this->data['name'] ?? '', $this->data['surname'] ?? '');
	}
}
