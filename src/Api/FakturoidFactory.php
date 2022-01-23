<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\ProjectSetting;
use Fakturoid\Client;

class FakturoidFactory
{
	public function __construct(
		private string $defaultUserAgent = 'Shoptet Doplnek - DEV <jsem@tomaskulhanek>'
	) {
	}

	public function createClientFromSetting(ProjectSetting $projectSettings): Client
	{
		return $this->createClient(
			$projectSettings->getAccountingAccount(),
			$projectSettings->getAccountingEmail(),
			$projectSettings->getAccountingApiKey()
		);
	}

	public function createClient(string $account, string $email, string $apiKey): Client
	{
		return new Client(
			$account,
			$email,
			$apiKey,
			$this->defaultUserAgent
		);
	}
}
