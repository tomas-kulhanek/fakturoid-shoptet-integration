<?php

declare(strict_types=1);


namespace App\Utils\Validator\Fakturoid;

use App\Utils\Validator\IpUtils;
use Nette\Http\IRequest;

class InitiatorValidator implements InitiatorValidatorInterface
{
	public function validateIpAddress(IRequest $request): bool
	{
		return IpUtils::checkIp($request->getRemoteAddress(), $this->getIps());
	}

	/**
	 * @return string[]
	 */
	protected function getIps(): array
	{
		$dnsRecords = dns_get_record("app.fakturoid.cz", DNS_A);
		if ($dnsRecords === false) {
			return [];
		}
		return array_column($dnsRecords, 'ip');
	}
}
