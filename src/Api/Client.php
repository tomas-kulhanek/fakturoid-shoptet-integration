<?php

declare(strict_types=1);


namespace App\Api;

use App\Application;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\AccessToken;
use App\DTO\Shoptet\ChangeDataResponse;
use App\DTO\Shoptet\ChangesResponse;
use App\DTO\Shoptet\ConfirmInstallation;
use App\DTO\Shoptet\CreditNote\CreditNote;
use App\DTO\Shoptet\CreditNote\CreditNoteDataResponse;
use App\DTO\Shoptet\Customer\Customer;
use App\DTO\Shoptet\Customer\CustomerDataResponse;
use App\DTO\Shoptet\EshopInfo\EshopInfoDataResponse;
use App\DTO\Shoptet\Invoice\Invoice;
use App\DTO\Shoptet\Invoice\InvoiceDataResponse;
use App\DTO\Shoptet\Oauth\OauthDataResponse;
use App\DTO\Shoptet\Oauth\OauthResponse;
use App\DTO\Shoptet\Order\ChangeOrderStatusDataRequest;
use App\DTO\Shoptet\Order\ChangeOrderStatusRequest;
use App\DTO\Shoptet\Order\Order;
use App\DTO\Shoptet\Order\OrderDataResponse;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoice;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoiceDataResponse;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\DTO\Shoptet\Webhooks\WebhookCreatedResponse;
use App\Exception\RuntimeException;
use App\Log\ActionLog;
use App\Manager\AccessTokenManager;
use App\Mapping\EntityMapping;
use App\Security\SecretVault\ISecretVault;
use Contributte\Guzzlette\ClientFactory;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Nette\Application\LinkGenerator;
use Nette\Caching\Cache;
use Nette\Caching\Storage;
use Nette\Http\Url;
use Psr\Http\Message\ResponseInterface;

class Client extends AbstractClient
{
	private const API_ENDPOINT_URL = 'https://api.myshoptet.com';

	private Cache $cache;

	private \GuzzleHttp\Client $httpClient;

	/**
	 * @param string $clientId
	 * @param string $clientSecret
	 * @param string $partnerProjectUrl
	 * @param array<string, string|int> $defaultHeaders
	 * @param ClientFactory $clientFactory
	 * @param EntityMapping $entityMapping
	 * @param LinkGenerator $urlGenerator
	 * @param Storage $storage
	 * @param ISecretVault $secretVault
	 */
	public function __construct(
		protected string           $clientId,
		protected string           $clientSecret,
		protected string           $partnerProjectUrl,
		protected array            $defaultHeaders,
		ClientFactory              $clientFactory,
		private EntityMapping      $entityMapping,
		private LinkGenerator      $urlGenerator,
		private Storage            $storage,
		private ISecretVault       $secretVault,
		private AccessTokenManager $accessTokenManager,
		protected ActionLog        $actionLog
	) {
		$this->httpClient = $clientFactory->createClient(['headers' => $defaultHeaders]);
		$this->cache = new Cache($this->storage, 'tokens');
	}

	protected function getHttpClient(): \GuzzleHttp\Client
	{
		return $this->httpClient;
	}

	public function getClientId(): string
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

	public function findCustomer(string $guid, Project $project): Customer
	{
		$this->actionLog->log($project, ActionLog::SHOPTET_CUSTOMER_DETAIL, $guid);
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/customers/' . $guid
			)->getBody()->getContents(),
			CustomerDataResponse::class
		);

		return $response->data->customer;
	}

	public function getProformaInvoiceChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse
	{
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/proforma-invoices/changes',
				params: ['page' => $page, 'from' => $from->format(DATE_RFC3339)]
			)->getBody()->getContents(),
			ChangeDataResponse::class
		);
		return $response->data;
	}

	public function getInvoiceChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse
	{
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/invoices/changes',
				params: ['page' => $page, 'from' => $from->format(DATE_RFC3339)]
			)->getBody()->getContents(),
			ChangeDataResponse::class
		);
		return $response->data;
	}

	public function getOrderChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse
	{
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/orders/changes',
				params: ['page' => $page, 'from' => $from->format(DATE_RFC3339)]
			)->getBody()->getContents(),
			ChangeDataResponse::class
		);
		return $response->data;
	}

	public function getCustomerChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse
	{
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/customers/changes',
				params: ['page' => $page, 'from' => $from->format(DATE_RFC3339)]
			)->getBody()->getContents(),
			ChangeDataResponse::class
		);
		return $response->data;
	}

	public function getEshopInfoFromAccessToken(AccessToken $accessToken, Url $shopUrl): OauthResponse
	{
		$responseData = $this->getHttpClient()->request(
			method: 'POST',
			uri: sprintf('%s%s', $shopUrl->getAbsoluteUrl(), 'resource?method=getBasicEshop'),
			options: [
				RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $accessToken->access_token],
			]
		)->getBody()->getContents();

		/** @var OauthDataResponse $response */
		$response = $this->entityMapping->createEntity(
			$responseData,
			OauthDataResponse::class
		);
		if (!$response->success) {
			throw new RuntimeException();
		}

		return $response->data;
	}

	public function getEshopInfo(Project $project): EshopInfoDataResponse
	{
		return
			$this->entityMapping->createEntity(
				$this->sendRequest(
					method: 'GET',
					project: $project,
					uri: '/api/eshop',
					params: ['include' => 'orderStatuses']
				)->getBody()->getContents(),
				EshopInfoDataResponse::class
			);
	}

	public function findOrder(string $code, Project $project): Order
	{
		$this->actionLog->log($project, ActionLog::SHOPTET_ORDER_DETAIL, $code);
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
		$this->actionLog->log($project, ActionLog::SHOPTET_CREDIT_NOTE_DETAIL, $code);
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
		$this->actionLog->log($project, ActionLog::SHOPTET_INVOICE_DETAIL, $code);
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
		$this->actionLog->log($project, ActionLog::SHOPTET_PROFORMA_DETAIL, $code);
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

	/**
	 * @param string $method
	 * @param Project $project
	 * @param string $uri
	 * @param string|null $data
	 * @param array<string, string|int|bool> $params
	 * @return ResponseInterface
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	protected function sendRequest(string $method, Project $project, string $uri, ?string $data = null, array $params = []): ResponseInterface
	{
		$accessToken = $this->getAccessToken($project);
		// todo osetrit i errorCode
		$response = $this->getHttpClient()->request(
			method: $method,
			uri: sprintf('%s%s', self::API_ENDPOINT_URL, $uri),
			options: [
				RequestOptions::HEADERS => [
					'Content-Type' => 'application/vnd.shoptet.v1.0',
					'Shoptet-Access-Token' => $this->secretVault->decrypt($accessToken->getAccessToken()),
				],
				RequestOptions::BODY => $data,
				RequestOptions::QUERY => $params,
			]
		);
		$this->accessTokenManager->returnToken($accessToken);
		return $response;
	}

	protected function returnAccessToken(\App\Database\Entity\Shoptet\AccessToken $accessToken): void
	{
		$this->accessTokenManager->returnToken($accessToken);
	}

	protected function getAccessToken(Project $project): \App\Database\Entity\Shoptet\AccessToken
	{
		$response = $this->accessTokenManager->leaseToken($project);
		return $response;
	}

	public function unregisterWebHooks(int $webhookId, Project $project): void
	{
		$this->sendRequest(
			method: 'DELETE',
			project: $project,
			uri: '/api/webhooks/' . $webhookId
		)->getBody()->getContents();
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
			'redirect_uri' => $this->urlGenerator->link(Application::DESTINATION_INSTALLATION_CONFIRM),
			'scope' => 'api',
		];
		try {
			$response = $this->getHttpClient()->request(
				'POST',
				$this->partnerProjectUrl . '/token',
				[
					RequestOptions::JSON => $data,
				]
			);

			/** @var ConfirmInstallation $result */
			$result = $this->getEntityMapping()->createEntity($response->getBody()->getContents(), ConfirmInstallation::class);
			return $result;
		} catch (\Throwable $exception) {
			throw $exception;
		}
	}

	public function updateOrderStatus(Project $project, string $orderCode, OrderStatus $newStatus): Order
	{
		$statusRequest = new ChangeOrderStatusDataRequest();
		$statusRequest->data = new ChangeOrderStatusRequest();
		$statusRequest->data->statusId = $newStatus->getShoptetId();
		$data = $this->entityMapping->serialize($statusRequest);
		bdump($data);

		//$this->actionLog->log($project, ActionLog::SHOPTET_CREDIT_NOTE_DETAIL, $orderCode);
		$newOrderData = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'PATCH',
				project: $project,
				uri: sprintf('/api/orders/%s/status', $orderCode),
				data: $data,
				params: ['suppressEmailSending' => 'true', 'suppressSmsSending' => 'true']
			)->getBody()->getContents(),
			OrderDataResponse::class
		);
		return $newOrderData->data->order;
	}
}
