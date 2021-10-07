<?php

declare(strict_types=1);

namespace App\Modules\Shoptet;

use App\Application;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
abstract class BaseShoptetPresenter extends BaseAppPresenter
{
	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('Shoptet')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
		if (!$this->getUser()->getProjectEntity()->isActive()) {
			$this->flashError('You cannot access this without active project');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}
}
