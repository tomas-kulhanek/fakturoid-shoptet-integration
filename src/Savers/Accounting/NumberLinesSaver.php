<?php

declare(strict_types=1);


namespace App\Savers\Accounting;

use App\Database\Entity\Accounting\NumberLine;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;

class NumberLinesSaver
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	/**
	 * @param \stdClass[] $numberLines
	 */
	public function save(Project $project, array $numberLines): void
	{
		$hashes = [];
		/** @var \stdClass $item */
		foreach ($numberLines as $item) {
			$hashes[] = $item->id;
		}

		$persistedEntities = [];
		/** @var NumberLine[] $numberLineEntities */
		$numberLineEntities = $this->entityManager->getRepository(NumberLine::class)
			->createQueryBuilder('ba')
			->where('ba.project = :project')
			->setParameter('project', $project)
			->getQuery()->getResult();
		/** @var NumberLine $entity */
		foreach ($numberLineEntities as $entity) {
			if (!in_array($entity->getAccountingId(), $hashes, true)) {
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getAccountingId()] = $entity;
		}
		/** @var \stdClass $item */
		foreach ($numberLines as $item) {
			if (isset($persistedEntities[$item->id])) {
				$entity = $persistedEntities[$item->id];
			} else {
				$entity = new NumberLine($project);
				$this->entityManager->persist($entity);
			}
			$entity->setAccountingId($item->id);
			$entity->setDefault($item->default);
			$entity->setFormat($item->format);
		}

		$this->entityManager->flush();
	}
}
