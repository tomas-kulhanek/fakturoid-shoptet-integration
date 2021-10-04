<?php

declare(strict_types=1);


namespace App\Utils\Validator;

use Nette\Http\IRequest;

interface InitiatorValidatorInterface
{
	public function validateIpAddress(IRequest $request): bool;
}
