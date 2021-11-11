<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\InvoiceEET;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<InvoiceEET>
 * @method InvoiceEET|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceEET|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceEET[] findAll()
 * @method InvoiceEET[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceEETRepository extends AbstractRepository
{
}
