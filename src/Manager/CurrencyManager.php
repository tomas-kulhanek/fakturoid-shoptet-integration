<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\Shoptet\Currency;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use Doctrine\ORM\NoResultException;

class CurrencyManager
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	public function getByCurrency(Project $project, string $currency, bool $cashdesk): ?Currency
	{
		try {
			return $this->entityManager->getRepository(Currency::class)
				->createQueryBuilder('c')
				->where('c.project = :project')
				->andWhere('c.cashdesk = :cashdesk')
				->andWhere('c.code = :currencyCode')
				->setParameter('project', $project)
				->setParameter('currencyCode', $currency)
				->setParameter('cashdesk', $cashdesk)
				->getQuery()->getSingleResult();
		} catch (NoResultException) {
			return null;
		}
	}

	public function getDefaultCurrency(Project $project, bool $cashdesk): ?Currency
	{
		try {
			return $this->entityManager->getRepository(Currency::class)
				->createQueryBuilder('c')
				->where('c.project = :project')
				->andWhere('c.cashdesk = :cashdesk')
				->andWhere('c.isDefault = :isDefault')
				->setParameter('project', $project)
				->setParameter('isDefault', true)
				->setParameter('cashdesk', $cashdesk)
				->getQuery()->getSingleResult();
		} catch (NoResultException) {
			return null;
		}
	}
}
