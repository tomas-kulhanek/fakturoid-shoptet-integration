<?php

declare(strict_types=1);


namespace App\Modules\Api\Fakturoid;

use App\Modules\Base\UnsecuredPresenter;
use Nette\Http\IResponse;

class FakturoidPresenter extends UnsecuredPresenter
{
	public function actionWebhook(string $code): void
	{
		if (!$this->getRequest()->isMethod('POST')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		$this->sendPayload();
	}
}
