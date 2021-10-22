<?php

declare(strict_types=1);


namespace App\Modules\Api\Fakturoid;

use App\Modules\Base\UnsecuredPresenter;
use Maknz\Slack\Client;
use Nette\DI\Attributes\Inject;
use Nette\Http\IResponse;

class FakturoidPresenter extends UnsecuredPresenter
{
	#[Inject]
	public Client $slackClient;

	public function actionWebhook(string $code): void
	{
		if (!$this->getRequest()->isMethod('POST')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		$messageObject = $this->slackClient->createMessage();
		$messageObject->attach([
			'fallback' => $this->getHttpRequest()->getRawBody(),
			'text' => $this->getHttpRequest()->getRawBody(),
			'author_name' => 'Fakturoid - ' . $code,
			'fields' => [
				['title' => 'Code', 'value' => $code],
				['title' => 'Time', 'value' => (new \DateTimeImmutable())->format('d.m.Y H:i:s')],
			],
		])->send('New user feedback');
		$this->sendPayload();
	}
}
