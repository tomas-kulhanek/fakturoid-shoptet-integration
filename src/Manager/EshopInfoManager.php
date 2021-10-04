<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\EshopInfo\OrderStatuses;
use App\Savers\OrderStatusSaver;

class EshopInfoManager
{
	public function __construct(
		private ClientInterface $client,
		private OrderStatusSaver $orderStatusSaver
	) {
	}

	public function syncOrderStatuses(Project $project): void
	{
		$eshopInfo = $this->client->getEshopInfo($project);
		if (!$eshopInfo->hasErrors() && $eshopInfo->data->orderStatuses instanceof OrderStatuses) {
			$this->orderStatusSaver->save($project, $eshopInfo->data->orderStatuses);
		}
	}
}
