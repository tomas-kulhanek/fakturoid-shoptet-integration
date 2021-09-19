<?php

declare(strict_types=1);


namespace App\Database;

use Doctrine\ORM\EntityRepository;
use Nettrine\ORM\EntityManagerDecorator;

class EntityManager extends EntityManagerDecorator
{
	use TRepositories;

	public function getRepository($entityName): EntityRepository
	{
		return parent::getRepository($entityName);
	}
}
