<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\Currency;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<Currency>
 * @method Currency|null find($id, $lockMode = null, $lockVersion = null)
 * @method Currency|null findOneBy(array $criteria, array $orderBy = null)
 * @method Currency[] findAll()
 * @method Currency[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurrencyRepository extends AbstractRepository
{
}
