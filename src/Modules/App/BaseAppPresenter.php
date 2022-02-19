<?php

declare(strict_types=1);

namespace App\Modules\App;

use App\Application;
use App\Modules\Base\SecuredPresenter;

abstract class BaseAppPresenter extends SecuredPresenter
{
	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed(\App\Security\Authorizator\StaticAuthorizator::RESOURCE_HOME)) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}
}
