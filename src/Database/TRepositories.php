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
	public function getUserRepository(): UserRepository
	{
		/** @var UserRepository $userRepository */
		$userRepository = $this->getRepository(User::class);

		return $userRepository;
	}
}
