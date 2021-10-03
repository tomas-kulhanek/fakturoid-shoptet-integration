<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<InvoiceItem>
 * @method InvoiceItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method InvoiceItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method InvoiceItem[] findAll()
 * @method InvoiceItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InvoiceItemRepository extends AbstractRepository
{
}
