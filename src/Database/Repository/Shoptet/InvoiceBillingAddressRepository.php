<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\InvoiceBillingAddress;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<InvoiceBillingAddress>
 * @method InvoiceBillingAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceBillingAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceBillingAddress[] findAll()
 * @method InvoiceBillingAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceBillingAddressRepository extends AbstractRepository
{
}
