<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\ActionLog;

/**
 * @method ActionLog|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method ActionLog|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method ActionLog[] findAll()
 * @method ActionLog[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<ActionLog>
 */
class ActionLogRepository extends AbstractRepository
{
}
