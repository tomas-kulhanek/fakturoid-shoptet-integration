<?php

declare(strict_types=1);


namespace App\Savers\Accounting;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;

class BankAccountSaver
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	/**
	 * @param Project $project
	 * @param \stdClass[] $bankAccounts
	 */
	public function save(Project $project, array $bankAccounts): void
	{
		$hashes = [];
		/** @var \stdClass $item */
		foreach ($bankAccounts as $item) {
			$hashes[] = $item->id;
		}

		$persistedEntities = [];
		/** @var BankAccount[] $bankAccountsEntities */
		$bankAccountsEntities = $this->entityManager->getRepository(BankAccount::class)
			->createQueryBuilder('ba')
			->where('ba.project = :project')
			->setParameter('project', $project)
			->getQuery()->getResult();
		/** @var BankAccount $entity */
		foreach ($bankAccountsEntities as $entity) {
			if (!in_array($entity->getAccountingId(), $hashes, true)) {
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getAccountingId()] = $entity;
		}
		/** @var \stdClass $item */
		foreach ($bankAccounts as $item) {
			if (isset($persistedEntities[$item->id])) {
				$entity = $persistedEntities[$item->id];
			} else {
				$entity = new BankAccount($project);
				$this->entityManager->persist($entity);
			}
			$entity->setAccountingId($item->id);
			$entity->setCurrency($item->currency);
			$entity->setIban($item->iban);
			$entity->setName($item->name);
			$entity->setNumber($item->number);
			$entity->setSwift($item->swift_bic);
		}

		$this->entityManager->flush();
	}
}
