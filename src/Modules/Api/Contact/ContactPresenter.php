<?php

namespace App\Modules\Api\Contact;

use App\Manager\TicketManager;
use App\Modules\Base\UnsecuredPresenter;
use Nette\DI\Attributes\Inject;
use Nette\Http\IResponse;

class ContactPresenter extends UnsecuredPresenter
{
	#[Inject]
	public TicketManager $ticketManager;

	public function actionMail(): void
	{
		if (!$this->getRequest()->isMethod('POST')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		$data = json_decode($this->getHttpRequest()->getRawBody(), true);
		$this->ticketManager->sendFromWeb(
			$data['email'],
			$data['fullName'],
			$data['messageBody']
		);
		$this->sendPayload();
	}
}
