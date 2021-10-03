<?php

declare(strict_types=1);


namespace App\Utils\Validator;

use Nette\Http\IRequest;

class InitiatorValidator implements InitiatorValidatorInterface
{
	/** @var array|string[] */
	private array $installation = ['78.24.15.64/26', '93.185.110.117/28'];
	/** @var array|string[] */
	private array $webhooks = ['78.24.15.64/26', '93.185.110.117/28'];

	public function validateInstallation(IRequest $request): bool
	{
		return IpUtils::checkIp($request->getRemoteAddress(), $this->installation); //todo
	}

	public function validateWebhook(IRequest $request): bool
	{
		return IpUtils::checkIp($request->getRemoteAddress(), $this->webhooks);
	}
}
