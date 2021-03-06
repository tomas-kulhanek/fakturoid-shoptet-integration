<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\Accounting\NumberLine;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\MessageBus\SynchronizeMessageBusDispatcher;

class ProjectSettingsManager
{
	public function __construct(
		private EntityManager                   $entityManager,
		private WebhookManager                  $webhookManager,
		private EshopInfoManager                $eshopInfoManager,
		private SynchronizeMessageBusDispatcher $synchronizeMessageBusDispatcher
	) {
	}

	/**
	 * @param string[] $synchronize
	 */
	public function saveShoptetSettings(
		Project $project,
		int     $automatization,
		array   $synchronize
	): void {
		$settings = $project->getSettings();
		$settings->setAutomatization($automatization);

		$webhooks = new WebhookRegistrationRequest();
		if (!$settings->isShoptetSynchronizeOrders()) {
			$this->webhookManager->registerOrderHooks($webhooks, $project);
			$settings->setShoptetSynchronizeOrders(true);
		}

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

		if (in_array('creditNotes', $synchronize, true) && !$settings->isShoptetSynchronizeCreditNotes()) {
			$settings->setShoptetSynchronizeCreditNotes(true);
			$this->webhookManager->registerCreditNoteHooks($webhooks, $project);
		} elseif (!in_array('creditNotes', $synchronize, true) && $settings->isShoptetSynchronizeCreditNotes()) {
			$this->webhookManager->unregisterCreditNotesHooks($project);
			$settings->setShoptetSynchronizeCreditNotes(false);
		}

		if (count($webhooks->data) > 0) {
			$this->webhookManager->registerHooks($webhooks, $project);
		}

		$this->eshopInfoManager->syncBaseData($project);

		$startDate = (new \DateTimeImmutable());
		$this->synchronizeMessageBusDispatcher->dispatchCustomer($project, $startDate);
		$this->synchronizeMessageBusDispatcher->dispatchOrder($project, $startDate);
		if (in_array('proformaInvoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchProformaInvoice($project, $startDate);
			$project->setLastProformaSyncAt($startDate);
		}
		if (in_array('invoices', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchInvoice($project, $startDate);
			$project->setLastInvoiceSyncAt($startDate);
		}
		if (in_array('creditNotes', $synchronize, true)) {
			$this->synchronizeMessageBusDispatcher->dispatchCreditNotes($project, $startDate);
			$project->setLastCreditNoteSyncAt($startDate);
		}
		$this->entityManager->flush();
	}

	public function saveAccountingSettings(
		Project $project,
		string  $accountingEmail,
		string  $accountingAccount,
		int     $accountingNumberLineId,
		int     $accountingCreditNoteNumberLineId,
		string  $accountingInvoiceTags,
		string  $accountingProformaInvoiceTags,
		string  $accountingCreditNoteTags,
		//string  $accountingCustomerTags,
		bool    $accountingReminders = false,
		bool    $propagateDeliveryAddress = false,
		?string $accountingApiKey = null,
		bool    $enableAccountingUpdate = true,
		bool    $removeKey = false,
	): void {
		$projectSetting = $project->getSettings();
		if (!$removeKey) {
			if ($accountingApiKey !== null && $accountingApiKey !== '') {
				$projectSetting->setAccountingApiKey($accountingApiKey);
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
			/** @var NumberLine|false $selectedNumberLine */
			$selectedNumberLine = $project->getAccountingNumberLines()->filter(fn (NumberLine $numberLine) => $numberLine->getId() === $accountingNumberLineId)->first();
			if ($selectedNumberLine !== false) {
				$projectSetting->setAccountingNumberLine($selectedNumberLine);
				$projectSetting->setShoptetSynchronizeProformaInvoices(false);
				$this->webhookManager->unregisterProformaInvoiceHooks($project);
			}
		} else {
			$projectSetting->setAccountingNumberLine(null);
		}
		if ($accountingCreditNoteNumberLineId > 0) {
			/** @var NumberLine|false $selectedNumberLine */
			$selectedNumberLine = $project->getAccountingNumberLines()->filter(fn (NumberLine $numberLine) => $numberLine->getId() === $accountingCreditNoteNumberLineId)->first();
			if ($selectedNumberLine !== false) {
				$projectSetting->setAccountingCreditNoteNumberLine($selectedNumberLine);
			}
		} else {
			$projectSetting->setShoptetSynchronizeCreditNotes(false);
			//$this->webhookManager->unregisterCreditNotesHooks($project);
			$projectSetting->setAccountingCreditNoteNumberLine(null);
		}
		$projectSetting->setAccountingInvoiceTags($accountingInvoiceTags);
		$projectSetting->setAccountingProformaInvoiceTags($accountingProformaInvoiceTags);
		$projectSetting->setAccountingCreditNoteTags($accountingCreditNoteTags);
		//$projectSetting->setAccountingCustomerTags($accountingCustomerTags);
		$this->entityManager->flush();
	}
}
