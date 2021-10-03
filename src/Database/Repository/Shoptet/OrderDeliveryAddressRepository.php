<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\OrderDeliveryAddress;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<OrderDeliveryAddress>
 * @method OrderDeliveryAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderDeliveryAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderDeliveryAddress[] findAll()
 * @method OrderDeliveryAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderDeliveryAddressRepository extends AbstractRepository
{
}
