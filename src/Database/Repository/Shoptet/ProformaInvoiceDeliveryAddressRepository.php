<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ProformaInvoiceDeliveryAddress>
 * @method ProformaInvoiceDeliveryAddress|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProformaInvoiceDeliveryAddress|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProformaInvoiceDeliveryAddress[] findAll()
 * @method ProformaInvoiceDeliveryAddress[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProformaInvoiceDeliveryAddressRepository extends AbstractRepository
{
}
