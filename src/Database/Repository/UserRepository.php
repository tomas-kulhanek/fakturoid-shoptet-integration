<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;

/**
 * @method User|NULL find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method User|NULL findOneBy(array $criteria, array $orderBy = null)
 * @method User[] findAll()
 * @method User[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends AbstractRepository<User>
 */
class UserRepository extends AbstractRepository
{
	public function findOneByEmailAndProject(string $email, Project $project): ?User
	{
		return $this->findOneBy(['email' => $email, 'project' => $project]);
	}
}
