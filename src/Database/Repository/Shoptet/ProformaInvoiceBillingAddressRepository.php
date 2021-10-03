<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\ProformaInvoiceBillingAddress;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ProformaInvoiceBillingAddress>
 * @method ProformaInvoiceBillingAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProformaInvoiceBillingAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProformaInvoiceBillingAddress[] findAll()
 * @method ProformaInvoiceBillingAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformaInvoiceBillingAddressRepository extends AbstractRepository
{
}
