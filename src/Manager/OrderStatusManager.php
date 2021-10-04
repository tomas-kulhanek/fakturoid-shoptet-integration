<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\OrderStatus;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;

class OrderStatusManager
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	public function findByShoptetId(Project $project, int $id): OrderStatus
	{
		return $this->entityManager->getRepository(OrderStatus::class)
			->findOneBy(['project' => $project, 'shoptetId' => $id]);
	}

	public function find(Project $project, int $id): OrderStatus
	{
		return $this->entityManager->getRepository(OrderStatus::class)
			->findOneBy(['project' => $project, 'id' => $id]);
	}

	/**
	 * @param string $optionName
	 * @param string[] $ids
	 * @param Project $project
	 * @param mixed $newValue
	 */
	public function changeOption(string $optionName, array $ids, Project $project, mixed $newValue): void
	{
		$qb = $this->entityManager->getRepository(OrderStatus::class)
			->createQueryBuilder('o');
		$qb->update(OrderStatus::class, 'o')
			->set('o.' . $optionName, ':value')
			->setParameter('value', $newValue)
			->where('o.project = :project')
			->setParameter('project', $project)
			->andWhere($qb->expr()->in('o.id', $ids))
			->getQuery()->execute();
	}
}
