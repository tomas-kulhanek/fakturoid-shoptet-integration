<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\OrderStatus;

/**
 * @method OrderStatus|NULL find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method OrderStatus|NULL findOneBy(array $criteria, array $orderBy = null)
 * @method OrderStatus[] findAll()
 * @method OrderStatus[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends AbstractRepository<OrderStatus>
 */
class OrderStatusRepository extends AbstractRepository
{
}
