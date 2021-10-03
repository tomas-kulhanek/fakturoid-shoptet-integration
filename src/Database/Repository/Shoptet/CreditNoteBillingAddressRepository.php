<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\CreditNoteBillingAddress;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<CreditNoteBillingAddress>
 * @method CreditNoteBillingAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditNoteBillingAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditNoteBillingAddress[] findAll()
 * @method CreditNoteBillingAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditNoteBillingAddressRepository extends AbstractRepository
{
}
