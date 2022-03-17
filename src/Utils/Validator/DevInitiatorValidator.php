<?php

declare(strict_types=1);


namespace App\Utils\Validator;

use Nette\Http\IRequest;
use Tracy\Debugger;
use Tracy\ILogger;

class DevInitiatorValidator implements InitiatorValidatorInterface
{
	public function validateIpAddress(IRequest $request): bool
	{
		bdump($request->getRemoteAddress());
		Debugger::log($request->getRemoteAddress(),ILogger::CRITICAL);
		return true;
	}
}
