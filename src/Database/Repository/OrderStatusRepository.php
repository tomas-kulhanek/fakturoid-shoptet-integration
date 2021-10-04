<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\OrderStatus;

/**
 * @method OrderStatus|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method OrderStatus|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method OrderStatus[] findAll()
 * @method OrderStatus[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<OrderStatus>
 */
class OrderStatusRepository extends AbstractRepository
{
}
