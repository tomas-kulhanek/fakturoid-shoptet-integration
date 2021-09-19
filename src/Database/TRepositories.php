<?php

declare(strict_types=1);


namespace App\Database;

use App\Database\Entity\File;
use App\Database\Entity\User;
use App\Database\Repository\FileRepository;
use App\Database\Repository\UserRepository;

/**
 * @mixin EntityManager
 */
trait TRepositories
{
	public function getUserRepository(): UserRepository
	{
		return $this->getRepository(User::class);
	}

	public function getFileRepository(): FileRepository
	{
		return $this->getRepository(File::class);
	}
}
