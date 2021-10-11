<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\ConfirmInstallation;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\DTO\Shoptet\Webhooks\WebhookCreatedResponse;
use App\DTO\Shoptet\Webhooks\WebhookDataResponse;
use App\DTO\Shoptet\Webhooks\WebhookResponse;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;

class DevLocalClient extends Client
{
	private const DEV_API_URL = 'https://tk.requestcatcher.com/test';

	public function unregisterWebHooks(int $webhookId, Project $project): void
	{
		$this->getHttpClient()->request(
			method: 'DELETE',
			uri: sprintf('%s%s', self::DEV_API_URL, '/api/webhooks/' . $webhookId),
			options: [
				RequestOptions::HEADERS => [
					'Content-Type' => 'application/vnd.shoptet.v1.0',
				],
			]
		);
	}

	public function registerWebHooks(WebhookRegistrationRequest $registrationRequest, Project $project): WebhookCreatedResponse
	{
		try {
			$this->getHttpClient()->request(
				method: 'POST',
				uri: sprintf('%s%s', self::DEV_API_URL, '/api/webhooks'),
				options: [
					RequestOptions::HEADERS => [
						'Content-Type' => 'application/vnd.shoptet.v1.0',
					],
					RequestOptions::BODY => $this->getEntityMapping()->serialize($registrationRequest),
					RequestOptions::QUERY => [],
				]
			);

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
		} catch (ClientException $exception) {
			$registeredWebhooks = $this->getEntityMapping()->createEntity(
				$exception->getResponse()->getBody()->getContents(),
				WebhookCreatedResponse::class
			);
			if ($registeredWebhooks->hasErrors()) {
				foreach ($registeredWebhooks->errors as $error) {
					if ($error->errorCode !== 'webhook-exists') {
						throw  $exception;
					}
				}
			}
		}
		return $registeredWebhooks;
	}

	public function confirmInstallation(string $code): ConfirmInstallation
	{
		$data = [
			'client_id' => $this->getClientId(),
			'client_secret' => $this->getClientSecret(),
			'code' => $code,
			'grant_type' => 'authorization_code',
			'redirect_uri' => self::DEV_API_URL,
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
