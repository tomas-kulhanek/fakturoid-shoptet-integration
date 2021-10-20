<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\DTO\Shoptet\Request\Webhook;
use App\Log\ActionLog;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Order;
use App\Savers\OrderSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadOrderMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private OrderSaver $saver,
		private ActionLog $actionLog
	) {
	}

	public function __invoke(Order $order): void
	{
		dump(get_class($order));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($order->getEshopId());
		switch ($order->getEventType()) {
			case Webhook::TYPE_ORDER_CREATE:
			case Webhook::TYPE_ORDER_UPDATE:
				$orderData = $this->client->findOrder(
					$order->getEventInstance(),
					$project
				);
				$order = $this->saver->save($project, $orderData);
				$this->actionLog->log($project, ActionLog::SHOPTET_ORDER_DETAIL, $order->getId());
				break;
			case Webhook::TYPE_ORDER_DELETE:
				//todo delete
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
