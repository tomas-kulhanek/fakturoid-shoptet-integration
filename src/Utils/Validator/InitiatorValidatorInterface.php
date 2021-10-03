<?php

declare(strict_types=1);


namespace App\Utils\Validator;

use Nette\Http\IRequest;

interface InitiatorValidatorInterface
{
	public function validateInstallation(IRequest $request): bool;

	public function validateWebhook(IRequest $request): bool;
}
