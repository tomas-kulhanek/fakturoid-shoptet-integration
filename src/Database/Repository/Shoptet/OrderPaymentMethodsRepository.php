<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\OrderPaymentMethods;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<OrderPaymentMethods>
 * @method OrderPaymentMethods|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderPaymentMethods|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderPaymentMethods[] findAll()
 * @method OrderPaymentMethods[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderPaymentMethodsRepository extends AbstractRepository
{
}
