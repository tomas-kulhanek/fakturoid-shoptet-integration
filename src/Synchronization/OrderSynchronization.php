<?php

declare(strict_types=1);


namespace App\Synchronization;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\ChangeResponse;
use App\Manager\OrderManager;

class OrderSynchronization
{
	public function __construct(
		private ClientInterface $client,
		private OrderManager    $orderManager,
		private EntityManager   $entityManager
	)
	{
	}

	public function synchronize(Project $project, \DateTimeImmutable $from): int
	{
		$totalSynchronized = 0;
		$response = $this->client->getOrderChanges($project, $from);

		/** @var ChangeResponse $change */
		foreach ($response->changes as $change) {
			$entity = $this->orderManager->findByShoptet($project, $change->code);
			if ($entity instanceof Order) {
				if ($entity->getChangeTime() >= $change->changeTime) {
					continue;
				}
			}
			$this->orderManager->synchronizeFromShoptet($project, $change->code);
			$totalSynchronized++;
		}
		$projectId = $project->getId();
		$this->entityManager->clear();
		$project = $this->entityManager->getRepository(Project::class)->findOneBy(['id' => $projectId]);
		$total = $response->paginator->page * $response->paginator->itemsPerPage;

		while ($response->paginator->totalCount > $total) {
			$response = $this->client->getOrderChanges($project, $from, ($response->paginator->page + 1));
			/** @var ChangeResponse $change */
			foreach ($response->changes as $change) {
				$entity = $this->orderManager->findByShoptet($project, $change->code);
				if ($entity instanceof Order) {
					if ($entity->getChangeTime() >= $change->changeTime) {
						continue;
					}
				}
				$this->orderManager->synchronizeFromShoptet($project, $change->code);
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
