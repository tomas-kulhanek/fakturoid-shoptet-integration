<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\CreditNote;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<CreditNote>
 * @method CreditNote|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditNote|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditNote[] findAll()
 * @method CreditNote[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditNoteRepository extends AbstractRepository
{
}
