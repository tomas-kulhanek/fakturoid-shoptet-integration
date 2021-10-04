<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\ProjectSetting;

/**
 * @method ProjectSetting|NULL find($id, ?int $lockMode = NULL, ?int $lockVersion = NULL)
 * @method ProjectSetting|NULL findOneBy(array $criteria, array $orderBy = NULL)
 * @method ProjectSetting[] findAll()
 * @method ProjectSetting[] findBy(array $criteria, array $orderBy = NULL, ?int $limit = NULL, ?int $offset = NULL)
 * @extends AbstractRepository<ProjectSetting>
 */
class ProjectSettingRepository extends AbstractRepository
{
}
