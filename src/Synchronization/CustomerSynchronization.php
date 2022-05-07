<?php

declare(strict_types=1);


namespace App\Synchronization;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\ChangeResponse;
use App\Manager\CustomerManager;

class CustomerSynchronization
{
	public function __construct(
		private ClientInterface $client,
		private CustomerManager $customerManager
	) {
	}

	public function synchronize(Project $project, \DateTimeImmutable $from): int
	{
		$totalSynchronized = 0;
		$response = $this->client->getCustomerChanges($project, $from);

		/** @var ChangeResponse $change */
		foreach ($response->changes as $change) {
			$entity = $this->customerManager->findByShoptetGuid($project, $change->guid);
			if ($entity instanceof Customer) {
				if ($entity->getChangeTime() >= $change->changeTime) {
					continue;
				}
			}
			$this->customerManager->synchronizeFromShoptet($project, $change->guid);
			$totalSynchronized++;
		}
		$total = $response->paginator->page * $response->paginator->itemsPerPage;

		while ($response->paginator->totalCount > $total) {
			$response = $this->client->getCustomerChanges($project, $from, ($response->paginator->page + 1));

			/** @var ChangeResponse $change */
			foreach ($response->changes as $change) {
				$entity = $this->customerManager->findByShoptetGuid($project, $change->guid);
				if ($entity instanceof Customer) {
					if ($entity->getChangeTime() >= $change->changeTime) {
						continue;
					}
				}
				$this->customerManager->synchronizeFromShoptet($project, $change->guid);
				$totalSynchronized++;
			}
			$total = $response->paginator->page * $response->paginator->itemsPerPage;
		}

		return $totalSynchronized;
	}
}
