<?php

declare(strict_types=1);

namespace App\Database\Repository;

use App\Database\Entity\ProjectSetting;

/**
 * @method ProjectSetting|NULL find($id, ?int $lockMode = null, ?int $lockVersion = null)
 * @method ProjectSetting|NULL findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectSetting[] findAll()
 * @method ProjectSetting[] findBy(array $criteria, array $orderBy = null, ?int $limit = null, ?int $offset = null)
 * @extends AbstractRepository<ProjectSetting>
 */
class ProjectSettingRepository extends AbstractRepository
{
}
