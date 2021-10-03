<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\CreditNoteItem;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<CreditNoteItem>
 * @method CreditNoteItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method CreditNoteItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method CreditNoteItem[] findAll()
 * @method CreditNoteItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreditNoteItemRepository extends AbstractRepository
{
}
