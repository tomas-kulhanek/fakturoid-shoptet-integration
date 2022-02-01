<?php

declare(strict_types=1);


namespace App\Synchronization;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\ChangeResponse;
use App\Manager\InvoiceManager;
use App\MessageBus\AccountingBusDispatcher;

class InvoiceSynchronization
{
	public function __construct(
		private ClientInterface         $client,
		private InvoiceManager          $invoiceManager,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private EntityManager           $entityManager
	) {
	}

	public function synchronize(Project $project, \DateTimeImmutable $from): int
	{
		$totalSynchronized = 0;
		$response = $this->client->getInvoiceChanges($project, $from);

		/** @var ChangeResponse $change */
		foreach ($response->changes as $change) {
			$entity = $this->invoiceManager->findByShoptet($project, $change->code);
			if ($entity instanceof Invoice) {
				if ($entity->getChangeTime() >= $change->changeTime) {
					continue;
				}
			}
			$invoice = $this->invoiceManager->synchronizeFromShoptet($project, $change->code);
			if ($invoice instanceof Invoice && $project->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
				$this->accountingBusDispatcher->dispatch($invoice);
			}
			$totalSynchronized++;
		}
		$projectId = $project->getId();
		$project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $projectId]);
		$this->entityManager->clear();
		$total = $response->paginator->page * $response->paginator->itemsPerPage;

		while ($response->paginator->totalCount > $total) {
			$response = $this->client->getInvoiceChanges($project, $from, ($response->paginator->page + 1));
			/** @var ChangeResponse $change */
			foreach ($response->changes as $change) {
				$entity = $this->invoiceManager->findByShoptet($project, $change->code);
				if ($entity instanceof Invoice) {
					if ($entity->getChangeTime() >= $change->changeTime) {
						continue;
					}
				}
				$invoice = $this->invoiceManager->synchronizeFromShoptet($project, $change->code);
				if ($invoice instanceof Invoice && $project->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
					$this->accountingBusDispatcher->dispatch($invoice);
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
