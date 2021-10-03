<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\ConfirmInstallation;
use App\DTO\Shoptet\CreditNote\CreditNote;
use App\DTO\Shoptet\CreditNote\CreditNoteDataResponse;
use App\DTO\Shoptet\Invoice\Invoice;
use App\DTO\Shoptet\Invoice\InvoiceDataResponse;
use App\DTO\Shoptet\Order\Order;
use App\DTO\Shoptet\Order\OrderDataResponse;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoice;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoiceDataResponse;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\DTO\Shoptet\Webhooks\WebhookCreatedResponse;
use App\Exception\RuntimeException;
use App\Mapping\EntityMapping;
use GuzzleHttp\Exception\ClientException;
use Nette\Application\LinkGenerator;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Psr\Http\Message\ResponseInterface;

class Client extends AbstractClient
{
	private const API_ENDPOINT_URL = 'https://api.myshoptet.com';

	private Cache $cache;

	public function __construct(
		protected string $clientId,
		protected string $clientSecret,
		protected string $partnerProjectUrl,
		private \GuzzleHttp\Client $httpClient,
		private EntityMapping $entityMapping,
		private LinkGenerator $urlGenerator,
		private Storage $storage
	) {
		$this->cache = new Cache($this->storage, 'tokens');
	}

	protected function getHttpClient(): \GuzzleHttp\Client
	{
		return $this->httpClient;
	}

	protected function getClientId(): string
	{
		return $this->clientId;
	}

	protected function getClientSecret(): string
	{
		return $this->clientSecret;
	}

	protected function getEntityMapping(): EntityMapping
	{
		return $this->entityMapping;
	}

	public function getEshopInfo(Project $project): void
	{
		dump(
			$this->sendRequest(
				'GET',
				$project,
				'/api/eshop'
			)->getBody()->getContents()
		);
	}

	public function findOrder(string $code, Project $project): Order
	{
		/** @var OrderDataResponse $response */
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/orders/' . $code
			)->getBody()->getContents(),
			OrderDataResponse::class
		);

		return $response->data->order;
	}

	public function findCreditNote(string $code, Project $project): CreditNote
	{
		/** @var CreditNoteDataResponse $response */
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/credit-notes/' . $code
			)->getBody()->getContents(),
			CreditNoteDataResponse::class
		);

		return $response->data->creditNote;
	}

	public function findInvoice(string $code, Project $project): Invoice
	{
		/** @var InvoiceDataResponse $response */
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/invoices/' . $code
			)->getBody()->getContents(),
			InvoiceDataResponse::class
		);

		return $response->data->invoice;
	}

	public function findProformaInvoice(string $code, Project $project): ProformaInvoice
	{
		/** @var ProformaInvoiceDataResponse $response */
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/proforma-invoices/' . $code
			)->getBody()->getContents(),
			ProformaInvoiceDataResponse::class
		);

		return $response->data->proformaInvoice;
	}

	protected function sendRequest(string $method, Project $project, string $uri, ?string $data = null): ResponseInterface
	{
		// todo osetrit i errorCode
		return $this->getHttpClient()->request(
			method: $method,
			uri: sprintf('%s%s', self::API_ENDPOINT_URL, $uri),
			options: [
				'headers' => [
					'Content-Type' => 'application/vnd.shoptet.v1.0',
					'Shoptet-Access-Token' => $this->getAccessToken($project),
				],
				'body' => $data,
			]
		);
	}

	protected function getAccessToken(Project $project): string
	{
		$key = sprintf('eshop-%d', $project->getEshopId());
		return $this->cache->load($key, function (&$dependencies) use ($project) {
			$response = json_decode(
				$this->getHttpClient()->request(
					method: 'GET',
					uri: $this->partnerProjectUrl . '/getAccessToken',
					options: [
						'headers' => ['Authorization' => 'Bearer ' . $project->getAccessToken()],
					]
				)->getBody()->getContents(),
				true
			);
			if (!isset($response['access_token'])) {
				throw new RuntimeException();
			}
			$dependencies[Cache::EXPIRE] = sprintf('%d minutes', $response['expires_in'] / 60);
			return $response['access_token'];
		});
	}

	public function registerWebHooks(WebhookRegistrationRequest $registrationRequest, Project $project): WebhookCreatedResponse
	{
		try {
			/** @var WebhookCreatedResponse $registeredWebhooks */
			$registeredWebhooks = $this->entityMapping->createEntity(
				$this->sendRequest(
					method: 'POST',
					project: $project,
					uri: '/api/webhooks',
					data: $this->entityMapping->serialize($registrationRequest)
				)->getBody()->getContents(),
				WebhookCreatedResponse::class
			);
		} catch (ClientException $exception) {
			$registeredWebhooks = $this->entityMapping->createEntity(
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
			'redirect_uri' => $this->urlGenerator->link('Api:Shoptet:installation'),
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
