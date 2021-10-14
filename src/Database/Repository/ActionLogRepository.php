<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\ActionLog;

/**
 * @method ActionLog|NULL find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method ActionLog|NULL findOneBy(array $criteria, array $orderBy = null)
 * @method ActionLog[] findAll()
 * @method ActionLog[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends AbstractRepository<ActionLog>
 */
class ActionLogRepository extends AbstractRepository
{
}
