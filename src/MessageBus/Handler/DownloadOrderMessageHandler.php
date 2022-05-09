<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\Database\EntityManager;
use App\DTO\Shoptet\Order\OrderResponse;
use App\DTO\Shoptet\Request\Webhook;
use App\Log\ActionLog;
use App\Manager\OrderManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Order;
use App\Savers\OrderSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadOrderMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager  $projectManager,
		private OrderSaver      $saver,
		private OrderManager    $orderManager,
		private EntityManager   $entityManager,
		private ActionLog       $actionLog
	) {
	}

	public function __invoke(Order $order): void
	{
		dump($order::class);
		dump($this::class);
		$project = $this->projectManager->getByEshopId($order->getEshopId());
		switch ($order->getEventType()) {
			case Webhook::TYPE_ORDER_CREATE:
			case Webhook::TYPE_ORDER_UPDATE:
				$orderData = $this->client->findOrder(
					$order->getEventInstance(),
					$project
				);
				if ($orderData->data instanceof OrderResponse) {
					$order = $this->saver->save($project, $orderData->data->order);
					$this->actionLog->logOrder($project, ActionLog::SHOPTET_ORDER_DETAIL, $order);
				}
				break;
			case Webhook::TYPE_ORDER_DELETE:
				$orderEntity = $this->orderManager->findByShoptet($project, $order->getEventInstance());
				if ($orderEntity instanceof \App\Database\Entity\Shoptet\Order) {
					$orderEntity->setDeletedAt(new \DateTimeImmutable());
					$this->entityManager->flush($orderEntity);
				}
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
