<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\DTO\Shoptet\Request\Webhook;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Order;
use App\Savers\OrderSaver;

class DownloadOrderMessageHandler
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private OrderSaver $saver
	) {
	}

	public function __invoke(Order $order): void
	{
		$project = $this->projectManager->getByEshopId($order->getEshopId());
		switch ($order->getEventType()) {
			case Webhook::TYPE_ORDER_CREATE:
			case Webhook::TYPE_ORDER_UPDATE:
				$orderData = $this->client->findOrder(
					$order->getEventInstance(),
					$project
				);
				$this->saver->save($project, $orderData);
				break;
			case Webhook::TYPE_ORDER_DELETE:
				//todo delete
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
