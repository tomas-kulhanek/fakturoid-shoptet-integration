<?php

declare(strict_types=1);


namespace App\Modules\Api\Shoptet;

use App\DTO\Shoptet\Request\Webhook;
use App\Manager\ProjectManager;
use App\Manager\WebhookManager;
use App\Mapping\EntityMapping;
use App\Utils\Validator\InitiatorValidatorInterface;
use Nette\Application\UI\Presenter;
use Nette\Http\Response;

class ShoptetPresenter extends Presenter
{
	public function __construct(
		private ProjectManager $projectManager,
		private WebhookManager $webhookManager,
		private EntityMapping $entityMapping,
		private InitiatorValidatorInterface $initiatorValidator
	) {
		parent::__construct();
	}

	public function actionInstall(): void
	{
		if (!$this->initiatorValidator->validateInstallation($this->getHttpRequest())) {
			$this->terminate();
		}
		/** @var string|null $code */
		$code = $this->getHttpRequest()->getQuery('code');
		if ($code === null || $code === '') {
			$this->terminate();
		}
		$project = $this->projectManager->confirmInstallation($code);
		$this->webhookManager->registerHooks($project);
		$this->sendPayload();
	}

	public function actionWebhook(): void
	{
		if (!$this->initiatorValidator->validateInstallation($this->getHttpRequest())) {
			$this->terminate();
		}
		//$this->checkSignature($request); todo
		/** @var Webhook $webhook */
		$webhook = $this->entityMapping->createEntity($this->getHttpRequest()->getRawBody(), Webhook::class);
		$project = $this->projectManager->getByEshopId($webhook->eshopId);
		$this->webhookManager->receive($webhook, $project);
		$this->sendPayload();
	}
}
