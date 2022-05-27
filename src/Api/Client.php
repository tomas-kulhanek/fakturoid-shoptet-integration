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
use App\DTO\Shoptet\CreditNote\CreditNoteDataResponse;
use App\DTO\Shoptet\Customer\CustomerDataResponse;
use App\DTO\Shoptet\EshopInfo\EshopInfoDataResponse;
use App\DTO\Shoptet\Invoice\InvoiceDataResponse;
use App\DTO\Shoptet\Oauth\OauthDataResponse;
use App\DTO\Shoptet\Oauth\OauthResponse;
use App\DTO\Shoptet\Order\ChangeOrderStatusDataRequest;
use App\DTO\Shoptet\Order\ChangeOrderStatusRequest;
use App\DTO\Shoptet\Order\Order;
use App\DTO\Shoptet\Order\OrderDataResponse;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoiceDataResponse;
use App\DTO\Shoptet\SignatureKey\SignatureKeyResponse;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\DTO\Shoptet\Webhooks\WebhookCreatedResponse;
use App\DTO\Shoptet\Webhooks\WebhookListResponse;
use App\Exception\RuntimeException;
use App\Manager\AccessTokenManager;
use App\Mapping\EntityMapping;
use Contributte\Guzzlette\ClientFactory;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use Nette\Application\LinkGenerator;
use Nette\Http\Url;
use Psr\Http\Message\ResponseInterface;

class Client extends AbstractClient
{
	private const API_ENDPOINT_URL = 'https://api.myshoptet.com';

	private \GuzzleHttp\Client $httpClient;

	/**
	 * @param array<string, string|int> $defaultHeaders
	 */
	public function __construct(
		protected string           $clientId,
		protected string           $clientSecret,
		protected string           $partnerProjectUrl,
		protected array            $defaultHeaders,
		ClientFactory              $clientFactory,
		private EntityMapping      $entityMapping,
		private LinkGenerator      $urlGenerator,
		private AccessTokenManager $accessTokenManager
	) {
		$this->httpClient = $clientFactory->createClient(['headers' => $defaultHeaders]);
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

	public function renewSignatureKey(Project $project): SignatureKeyResponse
	{
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'POST',
				project: $project,
				uri: '/api/webhooks/renew-signature-key'
			)->getBody()->getContents(),
			SignatureKeyResponse::class
		);
	}

	public function getOauthAccessToken(string $code, Url $shopUrl): AccessToken
	{
		/** @var AccessToken $responseData */
		$responseData = $this->getEntityMapping()->createEntity(
			$this->getHttpClient()->request(
				method: 'POST',
				uri: sprintf('%s%s', $shopUrl->getAbsoluteUrl(), 'token'),
				options: [
					RequestOptions::FORM_PARAMS => [
						'code' => $code,
						'grant_type' => 'authorization_code',
						'client_id' => $this->getClientId(),
						'client_secret' => $this->getClientSecret(),
						'redirect_uri' => $this->urlGenerator->link(Application::DESTINATION_OAUTH_CONFIRM),
						'scope' => 'basic_eshop',
					],
				]
			)->getBody()->getContents(),
			AccessToken::class
		);

		return $responseData;
	}

	public function findCustomer(string $guid, Project $project): CustomerDataResponse
	{
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/customers/' . $guid
			)->getBody()->getContents(),
			CustomerDataResponse::class
		);
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

	public function getCreditNoteChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse
	{
		$response = $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/credit-notes/changes',
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

	public function findOrder(string $code, Project $project): OrderDataResponse
	{
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/orders/' . $code
			)->getBody()->getContents(),
			OrderDataResponse::class
		);
	}

	public function findCreditNote(string $code, Project $project): CreditNoteDataResponse
	{
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/credit-notes/' . $code
			)->getBody()->getContents(),
			CreditNoteDataResponse::class
		);
	}

	public function findInvoice(string $code, Project $project): InvoiceDataResponse
	{
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/invoices/' . $code
			)->getBody()->getContents(),
			InvoiceDataResponse::class
		);
	}

	public function findProformaInvoice(string $code, Project $project): ProformaInvoiceDataResponse
	{
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/proforma-invoices/' . $code
			)->getBody()->getContents(),
			ProformaInvoiceDataResponse::class
		);
	}

	/**
	 * @param array<string, string|int|bool> $params
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	protected function sendRequest(string $method, Project $project, string $uri, ?string $data = null, array $params = []): ResponseInterface
	{
		$accessToken = $this->accessTokenManager->leaseToken($project);
		try {
			// todo osetrit i errorCode
			$response = $this->getHttpClient()->request(
				method: $method,
				uri: sprintf('%s%s', self::API_ENDPOINT_URL, $uri),
				options: [
					RequestOptions::HEADERS => [
						'Content-Type' => 'application/vnd.shoptet.v1.0',
						'Shoptet-Access-Token' => $accessToken->getAccessToken(),
					],
					RequestOptions::BODY => $data,
					RequestOptions::QUERY => $params,
				]
			);
		} catch (ClientException $exception) {
			$response = $exception->getResponse();
		}
		if ($response->getStatusCode() === 401) {
			$this->accessTokenManager->markAsInvalid($accessToken);
		} else {
			$this->accessTokenManager->returnToken($accessToken);
		}
		return $response;
	}

	public function getWebhooks(Project $project): WebhookListResponse
	{
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'GET',
				project: $project,
				uri: '/api/webhooks'
			)->getBody()->getContents(),
			WebhookListResponse::class
		);
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
		return $this->entityMapping->createEntity(
			$this->sendRequest(
				method: 'POST',
				project: $project,
				uri: '/api/webhooks',
				data: $this->entityMapping->serialize($registrationRequest)
			)->getBody()->getContents(),
			WebhookCreatedResponse::class
		);
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

	public function updateOrderStatus(Project $project, string $orderCode, ?OrderStatus $newStatus, ?bool $paid): Order
	{
		$statusRequest = new ChangeOrderStatusDataRequest();
		$statusRequest->data = new ChangeOrderStatusRequest();
		if ($newStatus instanceof OrderStatus) {
			$statusRequest->data->statusId = $newStatus->getShoptetId();
		}
		if($paid !== null){
			$statusRequest->data->paid = $paid;
		}
		$data = $this->entityMapping->serialize($statusRequest);

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
