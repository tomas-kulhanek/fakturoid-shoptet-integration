<?php

declare(strict_types=1);


namespace App\Synchronization;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\CreditNote;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\ChangeResponse;
use App\Manager\CreditNoteManager;
use App\Manager\ProformaInvoiceManager;
use App\MessageBus\AccountingBusDispatcher;

class CreditNoteSynchronization
{
	public function __construct(
		private ClientInterface         $client,
		private CreditNoteManager       $creditNoteManager,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private EntityManager           $entityManager
	) {
	}

	public function synchronize(Project $project, \DateTimeImmutable $from): int
	{
		$totalSynchronized = 0;
		$response = $this->client->getCreditNoteChanges($project, $from);

		/** @var ChangeResponse $change */
		foreach ($response->changes as $change) {
			$entity = $this->creditNoteManager->findByShoptet($project, $change->code);
			if ($entity instanceof CreditNote) {
				if ($entity->getChangeTime() >= $change->changeTime) {
					continue;
				}
			}
			$proformaInvoice = $this->creditNoteManager->synchronizeFromShoptet($project, $change->code);
			if ($proformaInvoice instanceof CreditNote && $project->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
				$this->accountingBusDispatcher->dispatch($proformaInvoice);
			}
			$totalSynchronized++;
		}
		$projectId = $project->getId();
		$this->entityManager->clear();
		$project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $projectId]);
		$total = $response->paginator->page * $response->paginator->itemsPerPage;

		while ($response->paginator->totalCount > $total) {
			$response = $this->client->getProformaInvoiceChanges($project, $from, ($response->paginator->page + 1));
			/** @var ChangeResponse $change */
			foreach ($response->changes as $change) {
				$entity = $this->creditNoteManager->findByShoptet($project, $change->code);
				if ($entity instanceof CreditNote) {
					if ($entity->getChangeTime() >= $change->changeTime) {
						continue;
					}
				}
				$proformaInvoice = $this->creditNoteManager->synchronizeFromShoptet($project, $change->code);
				if ($proformaInvoice instanceof CreditNote && $project->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
					$this->accountingBusDispatcher->dispatch($proformaInvoice);
				}
				$totalSynchronized++;
			}
			$total = $response->paginator->page * $response->paginator->itemsPerPage;

			$projectId = $project->getId();
			$this->entityManager->clear();
			$project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $projectId]);
		}

		return $totalSynchronized;
	}
}
