<?php

declare(strict_types=1);


namespace App\Synchronization;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\ChangeResponse;
use App\Manager\ProformaInvoiceManager;
use App\MessageBus\AccountingBusDispatcher;

class ProformaInvoiceSynchronization
{
	public function __construct(
		private ClientInterface         $client,
		private ProformaInvoiceManager  $invoiceManager,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private EntityManager           $entityManager
	) {
	}

	public function synchronize(Project $project, \DateTimeImmutable $from): int
	{
		$totalSynchronized = 0;
		$response = $this->client->getProformaInvoiceChanges($project, $from);

		/** @var ChangeResponse $change */
		foreach ($response->changes as $change) {
			$entity = $this->invoiceManager->findByShoptet($project, $change->code);
			if ($entity instanceof ProformaInvoice) {
				if ($entity->getChangeTime() >= $change->changeTime) {
					continue;
				}
			}
			$proformaInvoice = $this->invoiceManager->synchronizeFromShoptet($project, $change->code);
			if ($proformaInvoice instanceof ProformaInvoice && $project->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
				$this->accountingBusDispatcher->dispatch($proformaInvoice);
			}
			$totalSynchronized++;
		}
		$projectId = $project->getId();
		$project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $projectId]);
		$this->entityManager->clear();
		$total = $response->paginator->page * $response->paginator->itemsPerPage;

		while ($response->paginator->totalCount > $total) {
			$response = $this->client->getProformaInvoiceChanges($project, $from, ($response->paginator->page + 1));
			/** @var ChangeResponse $change */
			foreach ($response->changes as $change) {
				$entity = $this->invoiceManager->findByShoptet($project, $change->code);
				if ($entity instanceof ProformaInvoice) {
					if ($entity->getChangeTime() >= $change->changeTime) {
						continue;
					}
				}
				$proformaInvoice = $this->invoiceManager->synchronizeFromShoptet($project, $change->code);
				if ($proformaInvoice instanceof ProformaInvoice && $project->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
					$this->accountingBusDispatcher->dispatch($proformaInvoice);
				}
				$totalSynchronized++;
			}
			$total = $response->paginator->page * $response->paginator->itemsPerPage;

			$projectId = $project->getId();
			$project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $projectId]);
		}

		return $totalSynchronized;
	}
}
