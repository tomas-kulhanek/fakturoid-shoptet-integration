<?php

declare(strict_types=1);

namespace App\Database\Repository\Accounting;

use App\Database\Entity\Accounting\NumberLine;
use App\Database\Repository\AbstractRepository;

/**
 * @method NumberLine|NULL find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method NumberLine|NULL findOneBy(array $criteria, array $orderBy = null)
 * @method NumberLine[] findAll()
 * @method NumberLine[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends AbstractRepository<NumberLine>
 */
class NumberLineRepository extends AbstractRepository
{
}
