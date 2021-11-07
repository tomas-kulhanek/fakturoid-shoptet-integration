<?php

declare(strict_types=1);


namespace App\Modules\Api\Shoptet;

use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\Request\Webhook;
use App\Manager\ProjectManager;
use App\Manager\WebhookManager;
use App\Mapping\EntityMapping;
use App\Modules\Base\UnsecuredPresenter;
use App\Utils\Validator\InitiatorValidatorInterface;
use Nette\Http\IResponse;
use Tracy\Debugger;
use Tracy\ILogger;

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

	private function checkSignature(string $webhookBody, Project $project): void
	{
		if ($project->getSigningKey() === null || $project->getSigningKey() === '') {
			$this->projectManager->renewSigningKey($project);
		}
		$calculated = hash_hmac('sha1', $webhookBody, $project->getSigningKey());
		$expected = $this->getHttpRequest()->getHeader('Shoptet-Webhook-Signature');

		if ($calculated !== $expected) {
			$this->error('Unauthorized', IResponse::S401_UNAUTHORIZED);
		}
	}

	public function actionWebhook(): void
	{
		if (!$this->getRequest()->isMethod('POST')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		/** @var Webhook $webhook */
		$webhook = $this->entityMapping->createEntity($this->getHttpRequest()->getRawBody(), Webhook::class);
		$project = $this->projectManager->getByEshopId($webhook->eshopId);
		$this->checkSignature($this->getHttpRequest()->getRawBody(), $project);
		if ($project->isActive()) {
			$this->webhookManager->receive($webhook, $project);
		} else {
			Debugger::log(sprintf('Received webhook for inactive project %d', $project->getEshopId()), ILogger::WARNING);
		}
		$this->sendPayload();
	}
}
