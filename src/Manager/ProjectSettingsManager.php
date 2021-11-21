<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\MessageBus\SynchronizeMessageBusDispatcher;
use App\Security\SecretVault\ISecretVault;

class ProjectSettingsManager
{
	public function __construct(
		private ISecretVault                    $secretVault,
		private EntityManager                   $entityManager,
		private WebhookManager                  $webhookManager,
		private EshopInfoManager                $eshopInfoManager,
		private SynchronizeMessageBusDispatcher $synchronizeMessageBusDispatcher
	) {
	}

	/**
	 * @param Project $project
	 * @param int $automatization
	 * @param string[] $synchronize
	 */
	public function saveShoptetSettings(
		Project $project,
		int     $automatization,
		array   $synchronize
	): void {
		$settings = $project->getSettings();
		$settings->setAutomatization($automatization);

		$settings->setShoptetSynchronizeOrders(true);
		$webhooks = new WebhookRegistrationRequest();

		if (in_array('invoices', $synchronize, true) && !$settings->isShoptetSynchronizeInvoices()) {
			$settings->setShoptetSynchronizeInvoices(true);
			$this->webhookManager->registerInvoiceHooks($webhooks, $project);
		} elseif (!in_array('invoices', $synchronize, true) && $settings->isShoptetSynchronizeInvoices()) {
			$this->webhookManager->unregisterInvoiceHooks($project);
			$settings->setShoptetSynchronizeInvoices(false);
		}
		if (in_array('proformaInvoices', $synchronize, true) && !$settings->isShoptetSynchronizeProformaInvoices()) {
			$settings->setShoptetSynchronizeProformaInvoices(true);
			$this->webhookManager->registerProformaInvoiceHooks($webhooks, $project);
		} elseif (!in_array('proformaInvoices', $synchronize, true) && $settings->isShoptetSynchronizeProformaInvoices()) {
			$this->webhookManager->unregisterProformaInvoiceHooks($project);
			$settings->setShoptetSynchronizeProformaInvoices(false);
		}

		if (count($webhooks->data) > 0) {
			$this->webhookManager->registerHooks($webhooks, $project);
		}

		$this->eshopInfoManager->syncBaseData($project);

		$startDate = (new \DateTimeImmutable())->modify('-30 days');
		$this->synchronizeMessageBusDispatcher->dispatchCustomer($project, $startDate);
		$this->synchronizeMessageBusDispatcher->dispatchOrder($project, $startDate);
		if (in_array('invoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchInvoice($project, $startDate);
		}
		if (in_array('proformaInvoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchProformaInvoice($project, $startDate);
		}
	}

	public function saveAccountingSettings(
		Project $project,
		string  $accountingEmail,
		string  $accountingAccount,
		int     $accountingNumberLineId,
		bool    $accountingReminders = false,
		bool    $propagateDeliveryAddress = false,
		?string $accountingApiKey = null,
		bool $enableAccountingUpdate = true,
		bool    $removeKey = false,
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
		$projectSetting->setAccountingReminder($accountingReminders);
		$projectSetting->setAccountingAccount($accountingAccount);
		$projectSetting->setAccountingEmail($accountingEmail);
		$projectSetting->setPropagateDeliveryAddress($propagateDeliveryAddress);
		$projectSetting->setAccountingUpdate($enableAccountingUpdate);
		if ($accountingNumberLineId > 0) {
			$projectSetting->setAccountingNumberLineId($accountingNumberLineId);
			$projectSetting->setShoptetSynchronizeProformaInvoices(false);
			$this->webhookManager->unregisterProformaInvoiceHooks($project);
		} else {
			$projectSetting->setAccountingNumberLineId(null);
		}
		$this->entityManager->flush();
	}
}
