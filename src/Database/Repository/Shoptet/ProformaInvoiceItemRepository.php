<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\ProformaInvoiceItem;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ProformaInvoiceItem>
 * @method ProformaInvoiceItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProformaInvoiceItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProformaInvoiceItem[] findAll()
 * @method ProformaInvoiceItem[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformaInvoiceItemRepository extends AbstractRepository
{
}
