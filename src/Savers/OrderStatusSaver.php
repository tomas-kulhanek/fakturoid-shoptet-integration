<?php

declare(strict_types=1);


namespace App\Savers;

use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\EshopInfo\OrderStatus;
use App\DTO\Shoptet\EshopInfo\OrderStatuses;
use Doctrine\Common\Collections\Collection;

class OrderStatusSaver
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	public function save(Project $project, OrderStatuses $statuses): void
	{
		$hashes = [];
		/** @var OrderStatus $item */
		foreach ($statuses->getStatuses() as $item) {
			$hashes[] = $item->id;
		}

		$persistedEntities = [];
		/** @var \App\Database\Entity\OrderStatus $entity */
		foreach ($project->getOrderStatuses() as $entity) {
			if (!in_array($entity->getShoptetId(), $hashes, true) && $entity->getShoptetId() !== null) {
				$project->getOrderStatuses()->removeElement($entity);
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getShoptetId()] = $entity;
		}
		/** @var OrderStatus $item */
		foreach ($statuses->getStatuses() as $item) {
			if (isset($persistedEntities[$item->id])) {
				$entity = $persistedEntities[$item->id];
			} else {
				$entity = new \App\Database\Entity\OrderStatus($project);
				$this->entityManager->persist($entity);
				$project->getOrderStatuses()->add($entity);
			}
			$entity->setName($item->name);
			$entity->setMarkAsPaid($item->markAsPaid);
			$entity->setRank($item->order);
			$entity->setShoptetId($item->id);
			$entity->setIsDefault($item->id === $statuses->defaultStatus);
		}
		$this->entityManager->flush();
	}
}
