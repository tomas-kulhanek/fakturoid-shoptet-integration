<?php

declare(strict_types=1);


namespace App\Utils\Validator;

use Nette\Http\IRequest;

class DevInitiatorValidator implements InitiatorValidatorInterface
{
	public function validateInstallation(IRequest $request): bool
	{
		bdump($request->getRemoteAddress());
		return true;
	}

	public function validateWebhook(IRequest $request): bool
	{
		bdump($request->getRemoteAddress());
		return true;
	}
}
