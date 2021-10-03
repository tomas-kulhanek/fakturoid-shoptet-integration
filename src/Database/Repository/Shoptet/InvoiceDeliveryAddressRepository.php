<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<InvoiceDeliveryAddress>
 * @method InvoiceDeliveryAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceDeliveryAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceDeliveryAddress[] findAll()
 * @method InvoiceDeliveryAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceDeliveryAddressRepository extends AbstractRepository
{
}
