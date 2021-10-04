<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\OrderRepository;
use App\Event\OrderStatusChangeEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderManager
{
	public function __construct(
		private EntityManager $entityManager,
		private OrderStatusManager $orderStatusManager,
		private EventDispatcherInterface $eventDispatcher
	) {
	}

	public function getRepository(): OrderRepository
	{
		/** @var OrderRepository $repository */
		$repository = $this->entityManager->getRepository(Order::class);
		return $repository;
	}

	public function find(Project $project, int $id): Order
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'id' => $id]);
	}

	/**
	 * @param Project $project
	 * @param string[] $ids
	 * @param int $newStatus
	 */
	public function changeStatus(Project $project, array $ids, int $newStatus): void
	{
		$orders = $this->getRepository()->findBy(['project' => $project, 'id' => $ids]);
		$newStatusEntity = $this->orderStatusManager->find($project, $newStatus);
		foreach ($orders as $order) {
			if ($order->getStatus()->getId() === $newStatusEntity->getId()) {
				continue;
			}
			$event = new OrderStatusChangeEvent(
				$order,
				$order->getStatus(),
				$newStatusEntity
			);
			$order->setStatus($newStatusEntity);
			$this->entityManager->flush($order);
			$this->eventDispatcher->dispatch($event);
		}
	}
}
