<?php

declare(strict_types=1);

namespace App\Modules\Front\Home;

use App\Application;
use App\Modules\Base\BasePresenter;

final class HomePresenter extends BasePresenter
{
	protected function startup()
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
		}
		$this->redirect(Application::DESTINATION_SIGN_IN);
	}
}
