<?php

declare(strict_types=1);

namespace App\Modules\Admin;

use App\Application;
use App\Modules\Base\SecuredPresenter;

abstract class BaseAdminPresenter extends SecuredPresenter
{
	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('Admin:Home')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}
}
