<?php

declare(strict_types=1);


namespace App\Utils\Validator;

use Nette\Http\IRequest;

class InitiatorValidator implements InitiatorValidatorInterface
{
	/** @var array|string[] */
	private array $installation = ['78.24.15.64/26', '93.185.110.117/28'];

	public function validateIpAddress(IRequest $request): bool
	{
		return IpUtils::checkIp($request->getRemoteAddress(), $this->installation);
	}
}
