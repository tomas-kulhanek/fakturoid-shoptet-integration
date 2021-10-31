<?php

declare(strict_types=1);


namespace App\Savers\Shoptet;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO\Shoptet\EshopInfo\Currency;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\NoResultException;

class CurrencySaver
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	/**
	 * @param Project $project
	 * @param ArrayCollection<int, Currency>|Collection<int, Currency> $currencies
	 */
	public function save(Project $project, Collection $currencies): void
	{
		$this->processCurrencies($project, $currencies, false);
		$this->processCurrencies($project, $currencies, true);
		$this->entityManager->flush();
	}

	/**
	 * @param Project $project
	 * @param ArrayCollection<int, Currency>|Collection<int, Currency> $currencies
	 * @param bool $cashdesk
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	private function processCurrencies(Project $project, Collection $currencies, bool $cashdesk): void
	{
		$hashes = [];
		/** @var Currency $item */
		foreach ($currencies as $item) {
			$hashes[] = $item->code;
		}

		$persistedEntities = [];
		/** @var \App\Database\Entity\Shoptet\Currency $entity */
		foreach ($project->getCurrencies()->filter(fn (\App\Database\Entity\Shoptet\Currency $currency) => $currency->isCashdesk() === $cashdesk) as $entity) {
			if (!in_array($entity->getCode(), $hashes, true)) {
				$project->getCurrencies()->removeElement($entity);
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getCode()] = $entity;
		}
		/** @var Currency $item */
		foreach ($currencies as $item) {
			if (isset($persistedEntities[$item->code])) {
				$entity = $persistedEntities[$item->code];
			} else {
				$entity = new \App\Database\Entity\Shoptet\Currency($project);
				$project->getCurrencies()->add($entity);
				$this->entityManager->persist($entity);
			}
			$entity->setCode($item->code);
			$entity->setIsDefault($item->isDefault);
			$entity->setIsDefaultAdmin($item->isDefaultAdmin);
			$entity->setIsVisible($item->isVisible);
			$entity->setPriceDecimalPlaces($item->priceDecimalPlaces);
			$entity->setPriority($item->priority);
			$entity->setTitle($item->title);
			$entity->setCashdesk($cashdesk);
			if (!$entity->getBankAccount() instanceof BankAccount) {
				try {
					$ba = $this->entityManager->getRepository(BankAccount::class)
						->createQueryBuilder('ba')
						->where('ba.project = :project')
						->andWhere('ba.currency = :currencyCode')
						->andWhere('ba.number = :bankAccount')
						->setParameter('project', $project)
						->setParameter('currencyCode', $entity->getCode())
						->setParameter('bankAccount', $item->bankAccount->accountNumber)
						->getQuery()->getSingleResult();
					$entity->setBankAccount($ba);
				} catch (NoResultException) {
					try {
						$ba = $this->entityManager->getRepository(BankAccount::class)
							->createQueryBuilder('ba')
							->where('ba.project = :project')
							->andWhere('ba.system = 1')
							->setParameter('project', $project)
							->getQuery()->getSingleResult();
						$entity->setBankAccount($ba);
					} catch (NoResultException) {
					}
				}
			}
		}
	}
}
