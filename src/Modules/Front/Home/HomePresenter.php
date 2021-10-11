<?php

declare(strict_types=1);

namespace App\Modules\Front\Home;

use App\Application;
use App\Modules\Front\BaseFrontPresenter;

final class HomePresenter extends BaseFrontPresenter
{
	protected function startup()
	{
		$this->projectId = null;
		$this->redirect(':Front:Sign:in');
	}
}
