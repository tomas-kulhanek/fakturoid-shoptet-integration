<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\ConfirmInstallation;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\DTO\Shoptet\Webhooks\WebhookCreatedResponse;
use App\DTO\Shoptet\Webhooks\WebhookDataResponse;
use App\DTO\Shoptet\Webhooks\WebhookResponse;

class DevClient extends Client
{
	protected function getAccessToken(Project $project): string
	{
		return '470424-a-716-vigds7yb6mgllwrcroblv5o9i8wrobqo';
	}

	public function unregisterWebHooks(int $webhookId, Project $project): void
	{
	}

	public function registerWebHooks(WebhookRegistrationRequest $registrationRequest, Project $project): WebhookCreatedResponse
	{
		$response = new WebhookCreatedResponse();
		$response->data = new WebhookDataResponse();
		foreach ($registrationRequest->data as $requestedWebhook) {
			$reg = new WebhookResponse();
			$reg->event = $requestedWebhook->event;
			$reg->url = $requestedWebhook->url;
			$reg->created = new \DateTimeImmutable();
			$reg->id = time() - rand(100, 9999);
			$response->data->webhooks[] = $reg;
		}

		return $response;
	}

	public function confirmInstallation(string $code): ConfirmInstallation
	{
		$data = [
			'client_id' => $this->getClientId(),
			'client_secret' => $this->getClientSecret(),
			'code' => $code,
			'grant_type' => 'authorization_code',
			'redirect_uri' => 'https://fakturoid.helppc.cz/api/shoptet/installation',
			'scope' => 'api',
		];
		try {
			$response = $this->getHttpClient()->request(
				'POST',
				$this->partnerProjectUrl . '/token',
				[
					'json' => $data,
				]
			);

			/** @var ConfirmInstallation $result */
			$result = $this->getEntityMapping()->createEntity($response->getBody()->getContents(), ConfirmInstallation::class);
			return $result;
		} catch (\Throwable $exception) {
			throw $exception;
		}
	}
}
