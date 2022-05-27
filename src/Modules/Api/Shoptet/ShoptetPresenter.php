<?php

declare(strict_types=1);


namespace App\Modules\Api\Shoptet;

use App\DTO\Shoptet\Request\Webhook;
use App\Exception\Logic\NotFoundException;
use App\Manager\ProjectManager;
use App\Manager\WebhookManager;
use App\Mapping\EntityMapping;
use App\Modules\Base\UnsecuredPresenter;
use App\Utils\Validator\Shoptet\InitiatorValidatorInterface;
use GuzzleHttp\Exception\ClientException;
use Nette\Application\Responses\JsonResponse;
use Nette\Http\IResponse;
use Tracy\Debugger;
use Tracy\ILogger;

class ShoptetPresenter extends UnsecuredPresenter
{
	public function __construct(
		private ProjectManager              $projectManager,
		private WebhookManager              $webhookManager,
		private EntityMapping               $entityMapping,
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
			Debugger::log(sprintf('Divny request z %s na Shoptet', $this->getHttpRequest()->getRemoteAddress()), ILogger::CRITICAL);
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
		try {
			$this->projectManager->confirmInstallation($code);
		} catch (ClientException $exception) {
			Debugger::log($exception, ILogger::EXCEPTION);
			$jsonResponse = new JsonResponse($exception->getResponse()->getBody()->getContents());
			$this->getHttpResponse()->setCode($exception->getResponse()->getStatusCode());
			$this->sendResponse(
				$jsonResponse
			);
		}
		$this->sendPayload();
	}

	//private function checkSignature(string $webhookBody, Project $project): void
	//{
	//	if ($project->getSigningKey() === null || $project->getSigningKey() === '') {
	//		$this->projectManager->renewSigningKey($project);
	//		$this->error('Forbidden', IResponse::S422_UNPROCESSABLE_ENTITY);
	//	}
	//	$calculated = hash_hmac('sha1', $webhookBody, $project->getSigningKey());
	//	$expected = $this->getHttpRequest()->getHeader('Shoptet-Webhook-Signature');
	//
	//	if ($calculated !== $expected) {
	//		Debugger::log(sprintf('Signature is not valid for project %s, calculated is %s, and expected is %s. Data: %s', $project->getEshopId(), $calculated, $expected, $webhookBody), ILogger::CRITICAL);
	//		$this->error('Forbidden', IResponse::S403_FORBIDDEN);
	//	}
	//}

	public function actionWebhook(): void
	{
		if (!$this->getRequest()->isMethod('POST')) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		/** @var Webhook $webhook */
		$webhook = $this->entityMapping->createEntity($this->getHttpRequest()->getRawBody(), Webhook::class);
		try {
			$project = $this->projectManager->getByEshopId($webhook->eshopId);
		} catch (NotFoundException) {
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}
		//if (!in_array($webhook->event, $webhook->getAddonSystemEventTypes(), TRUE)) {
		//	$this->checkSignature($this->getHttpRequest()->getRawBody(), $project);
		//}
		if ($project->isSuspended()) {
			Debugger::log(sprintf('Received webhook for inactive project %d', $project->getEshopId()), ILogger::WARNING);
			$this->error('Forbidden', IResponse::S403_FORBIDDEN);
		}

		$this->webhookManager->receive($webhook, $project);
		$this->sendPayload();
	}
}
