<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\ProjectSetting;
use App\Security\SecretVault\ISecretVault;
use Fakturoid\Client;

class FakturoidFactory
{
	public function __construct(
		private ISecretVault $secretVault,
		private FakturoidRequester $fakturoidRequester
	) {
	}

	public function createClient(ProjectSetting $projectSettings): Client
	{
		return new Client(
			$projectSettings->getFakturoidAccount(),
			$projectSettings->getFakturoidEmail(),
			$this->secretVault->decrypt($projectSettings->getFakturoidApiKey()),
			'Shoptet Doplnek - DEV <jsem@tomaskulhanek>',
			['requester' => $this->fakturoidRequester]
		);
	}
}
