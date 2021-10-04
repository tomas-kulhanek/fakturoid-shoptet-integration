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
		private EntityManager $entityManager,
		private WebhookManager $webhookManager
	) {
	}

	public function saveSettings(
		Project $project,
		int $automatization,
		string $fakturoidEmail,
		string $fakturoidAccount,
		bool $propagateDeliveryAddress = false,
		?string $fakturoidApiKey = null,
		bool $removeKey = false
	): void {
		$projectSetting = $project->getSettings();
		if (!$removeKey) {
			if ($fakturoidApiKey !== null && $fakturoidApiKey !== '') {
				$projectSetting->setFakturoidApiKey(
					$this->secretVault->encrypt($fakturoidApiKey)
				);
			}
		} else {
			$projectSetting->setFakturoidApiKey(null);
		}
		$projectSetting->setAutomatization($automatization);
		$projectSetting->setFakturoidAccount($fakturoidAccount);
		$projectSetting->setFakturoidEmail($fakturoidEmail);
		$projectSetting->setPropagateDeliveryAddress($propagateDeliveryAddress);
		$this->entityManager->flush($projectSetting);

		if ($projectSetting->isSetRight()) {
			$this->webhookManager->registerHooks($project);
		} else {
			$this->webhookManager->unregisterHooks($project);
		}


		//todo webhooky
	}
}
