<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Security\SecretVault\ISecretVault;

class ProjectSettingsManager
{
	public function __construct(
		private ISecretVault $secretVault,
		private EntityManager $entityManager
	) {
	}

	public function saveSettings(
		Project $project,
		int $automatization,
		string $accountingEmail,
		string $accountingAccount,
		bool $propagateDeliveryAddress = false,
		?string $accountingApiKey = null,
		bool $removeKey = false
	): void {
		$projectSetting = $project->getSettings();
		if (!$removeKey) {
			if ($accountingApiKey !== null && $accountingApiKey !== '') {
				$projectSetting->setAccountingApiKey(
					$this->secretVault->encrypt($accountingApiKey)
				);
			}
		} else {
			$projectSetting->setAccountingApiKey(null);
		}
		$projectSetting->setAutomatization($automatization);
		$projectSetting->setAccountingAccount($accountingAccount);
		$projectSetting->setAccountingEmail($accountingEmail);
		$projectSetting->setPropagateDeliveryAddress($propagateDeliveryAddress);
		$this->entityManager->flush($projectSetting);
	}
}
