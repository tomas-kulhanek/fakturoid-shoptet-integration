<?php

declare(strict_types=1);

namespace App\Modules\App\Home;

use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
final class HomePresenter extends BaseAppPresenter
{
}
