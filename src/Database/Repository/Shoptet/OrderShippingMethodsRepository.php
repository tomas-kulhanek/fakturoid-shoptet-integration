<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\OrderShippingMethods;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<OrderShippingMethods>
 * @method OrderShippingMethods|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderShippingMethods|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderShippingMethods[] findAll()
 * @method OrderShippingMethods[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderShippingMethodsRepository extends AbstractRepository
{
}
