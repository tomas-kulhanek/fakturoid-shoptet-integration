<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\Order;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<Order>
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[] findAll()
 * @method Order[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends AbstractRepository
{
}
