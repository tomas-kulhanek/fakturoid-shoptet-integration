<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\ReceivedWebhook;
use App\Database\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends AbstractRepository<ReceivedWebhook>
 * @method ReceivedWebhook|null find($id, $lockMode = null, $lockVersion = null)
 * @method ReceivedWebhook|null findOneBy(array $criteria, array $orderBy = null)
 * @method ReceivedWebhook[] findAll()
 * @method ReceivedWebhook[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReceivedWebhookRepository extends AbstractRepository
{
}
