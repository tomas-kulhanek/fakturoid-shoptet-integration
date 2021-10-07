<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\ProjectSetting;
use App\Security\SecretVault\ISecretVault;
use Fakturoid\Client;

class FakturoidFactory
{
	public function __construct(
		private ISecretVault       $secretVault,
		private FakturoidRequester $accountingRequester
	) {
	}

	public function createClientFromSetting(ProjectSetting $projectSettings): Client
	{
		return $this->createClient(
			$projectSettings->getAccountingAccount(),
			$projectSettings->getAccountingEmail(),
			$this->secretVault->decrypt($projectSettings->getAccountingApiKey())
		);
	}

	public function createClient(string $account, string $email, string $apiKey): Client
	{
		return new Client(
			$account,
			$email,
			$apiKey,
			'Shoptet Doplnek - DEV <jsem@tomaskulhanek>',
			['requester' => $this->accountingRequester]
		);
	}
}
