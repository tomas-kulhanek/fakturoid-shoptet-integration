<?php

declare(strict_types=1);


namespace App\Database;

use App\Database\Entity\File;
use App\Database\Entity\User;
use App\Database\Repository\FileRepository;
use App\Database\Repository\UserRepository;
use Doctrine\ORM\EntityRepository;

/**
 * @mixin EntityManager
 */
trait TRepositories
{
	/**
	 * @return UserRepository
	 * @phpstan-return EntityRepository<User>
	 */
	public function getUserRepository(): EntityRepository
	{
		return $this->getRepository(User::class);
	}

	/**
	 * @return FileRepository
	 * @phpstan-return EntityRepository<File>
	 */
	public function getFileRepository(): EntityRepository
	{
		return $this->getRepository(File::class);
	}
}
