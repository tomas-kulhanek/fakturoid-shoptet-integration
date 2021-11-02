<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\OrderStatus;
use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\AccessToken;
use App\DTO\Shoptet\ChangesResponse;
use App\DTO\Shoptet\ConfirmInstallation;
use App\DTO\Shoptet\CreditNote\CreditNote;
use App\DTO\Shoptet\Customer\CustomerDataResponse;
use App\DTO\Shoptet\EshopInfo\EshopInfoDataResponse;
use App\DTO\Shoptet\Invoice\InvoiceDataResponse;
use App\DTO\Shoptet\Oauth\OauthResponse;
use App\DTO\Shoptet\Order\Order;
use App\DTO\Shoptet\Order\OrderDataResponse;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoiceDataResponse;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\DTO\Shoptet\Webhooks\WebhookCreatedResponse;
use App\DTO\Shoptet\Webhooks\WebhookListResponse;
use Nette\Http\Url;

interface ClientInterface
{
	public function getWebhooks(Project $project): WebhookListResponse;

	public function confirmInstallation(string $code): ConfirmInstallation;

	public function getCustomerChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse;

	public function getOrderChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse;

	public function getProformaInvoiceChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse;

	public function getInvoiceChanges(Project $project, \DateTimeImmutable $from, int $page = 1): ChangesResponse;

	public function findCustomer(string $guid, Project $project): CustomerDataResponse;

	public function unregisterWebHooks(int $webhookId, Project $project): void;

	public function registerWebHooks(WebhookRegistrationRequest $registrationRequest, Project $project): WebhookCreatedResponse;

	public function getEshopInfo(Project $project): EshopInfoDataResponse;

	public function getOauthAccessToken(string $code, Url $shopUrl): AccessToken;

	public function getEshopInfoFromAccessToken(AccessToken $accessToken, Url $shopUrl): OauthResponse;

	public function getClientId(): string;

	public function findProformaInvoice(string $code, Project $project): ProformaInvoiceDataResponse;

	public function findInvoice(string $code, Project $project): InvoiceDataResponse;

	public function findCreditNote(string $code, Project $project): CreditNote;

	public function updateOrderStatus(Project $project, string $orderCode, OrderStatus $newStatus): Order;

	public function findOrder(string $code, Project $project): OrderDataResponse;
}
