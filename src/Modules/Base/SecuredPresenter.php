<?php

declare(strict_types=1);


namespace App\Modules\Base;

use App\Application;
use Nette\Security\UserStorage;

abstract class SecuredPresenter extends BasePresenter
{
	public function checkRequirements(mixed $element): void
	{
		if (!$this->getUser()->isLoggedIn()) {
			if ($this->getUser()->getLogoutReason() === UserStorage::LOGOUT_INACTIVITY) {
				$this->flashInfo('You have been logged out for inactivity');
			}
			bdump($this->getUser());
			$this->redirect(
				Application::DESTINATION_SIGN_IN,
				['backlink' => $this->storeRequest()]
			);
		}
	}
}
