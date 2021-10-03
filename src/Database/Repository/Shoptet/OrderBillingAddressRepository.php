<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\OrderBillingAddress;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<OrderBillingAddress>
 * @method OrderBillingAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderBillingAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderBillingAddress[] findAll()
 * @method OrderBillingAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderBillingAddressRepository extends AbstractRepository
{
}
