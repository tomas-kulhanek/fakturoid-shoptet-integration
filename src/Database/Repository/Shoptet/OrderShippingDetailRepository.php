<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\OrderShippingDetail;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<OrderShippingDetail>
 * @method OrderShippingDetail|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderShippingDetail|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderShippingDetail[] findAll()
 * @method OrderShippingDetail[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderShippingDetailRepository extends AbstractRepository
{
}
