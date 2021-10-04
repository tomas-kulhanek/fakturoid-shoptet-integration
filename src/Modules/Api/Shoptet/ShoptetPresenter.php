<?php

declare(strict_types=1);


namespace App\Modules\Api\Shoptet;

use App\DTO\Shoptet\Request\Webhook;
use App\Manager\ProjectManager;
use App\Manager\WebhookManager;
use App\Mapping\EntityMapping;
use App\Modules\Base\UnsecuredPresenter;
use App\Utils\Validator\InitiatorValidatorInterface;
use Nette\Http\IResponse;

class ShoptetPresenter extends UnsecuredPresenter
{
	public function __construct(
		private ProjectManager $projectManager,
		private WebhookManager $webhookManager,
		private EntityMapping $entityMapping,
		private InitiatorValidatorInterface $initiatorValidator,
	) {
		parent::__construct();
	}

	/**
	 * @param mixed $element
	 * @throws \Nette\Application\BadRequestException
	 */
	public function checkRequirements($element): void
	{
		if (!$this->initiatorValidator->validateIpAddress($this->getHttpRequest())) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		parent::checkRequirements($element);
	}

	public function actionInstallation(?string $code): void
	{
		if ($code === null || $code === '') {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		if (!$this->getRequest()->isMethod('GET')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		$this->projectManager->confirmInstallation($code);
		$this->sendPayload();
	}

	public function actionWebhook(): void
	{
		if (!$this->getRequest()->isMethod('POST')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		//$this->checkSignature($request); todo
		/** @var Webhook $webhook */
		$webhook = $this->entityMapping->createEntity($this->getHttpRequest()->getRawBody(), Webhook::class);
		$project = $this->projectManager->getByEshopId($webhook->eshopId);
		$this->webhookManager->receive($webhook, $project);
		$this->sendPayload();
	}
}
