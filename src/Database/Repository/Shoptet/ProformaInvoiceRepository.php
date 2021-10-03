<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ProformaInvoice>
 * @method ProformaInvoice|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProformaInvoice|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProformaInvoice[] findAll()
 * @method ProformaInvoice[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformaInvoiceRepository extends AbstractRepository
{
}
