<?php

declare(strict_types=1);

namespace App\Database\Repository\Shoptet;

use App\Database\Entity\Shoptet\AccessToken;
use App\Database\Repository\AbstractRepository;

/**
 * @extends AbstractRepository<AccessToken>
 * @method AccessToken|null find($id, $lockMode = null, $lockVersion = null)
 * @method AccessToken|null findOneBy(array $criteria, array $orderBy = null)
 * @method AccessToken[] findAll()
 * @method AccessToken[] findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AccessTokenRepository extends AbstractRepository
{
}
