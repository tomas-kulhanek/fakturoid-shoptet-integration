<?php

declare(strict_types=1);

namespace App\Database\Repository\Accounting;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Repository\AbstractRepository;

/**
 * @method BankAccount|NULL find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method BankAccount|NULL findOneBy(array $criteria, array $orderBy = null)
 * @method BankAccount[] findAll()
 * @method BankAccount[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends AbstractRepository<BankAccount>
 */
class BankAccountRepository extends AbstractRepository
{
}
