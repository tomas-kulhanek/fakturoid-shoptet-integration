<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\CreditNoteDeliveryAddress;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<CreditNoteDeliveryAddress>
 * @method CreditNoteDeliveryAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditNoteDeliveryAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditNoteDeliveryAddress[] findAll()
 * @method CreditNoteDeliveryAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditNoteDeliveryAddressRepository extends AbstractRepository
{
}
