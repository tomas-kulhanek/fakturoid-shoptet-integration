<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\OrderRepository;
use App\Event\OrderStatusChangeEvent;
use App\Savers\OrderSaver;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class OrderManager
{
	public function __construct(
		private EntityManager            $entityManager,
		private OrderStatusManager       $orderStatusManager,
		private EventDispatcherInterface $eventDispatcher,
		private ClientInterface          $shoptetClient,
		private OrderSaver               $orderSaver
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

	public function findByShoptet(Project $project, string $shoptetCode): ?Order
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'shoptetCode' => $shoptetCode]);
	}

	public function synchronizeFromShoptet(Project $project, int $id): ?Order
	{
		$entity = $this->find($project, $id);

		$orderData = $this->shoptetClient->findOrder($entity->getCode(), $entity->getProject());
		bdump($orderData);
		$this->orderSaver->save($entity->getProject(), $orderData);
		$this->entityManager->refresh($entity);
		return $entity;
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
			$this->eventDispatcher->dispatch($event);
			$this->entityManager->flush($order);
		}
	}
}
