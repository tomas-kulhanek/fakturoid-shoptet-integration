<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\Shoptet\Project;
use App\DTO\Shoptet\AccessToken;
use App\DTO\Shoptet\ConfirmInstallation;
use App\DTO\Shoptet\CreditNote\CreditNote;
use App\DTO\Shoptet\Invoice\Invoice;
use App\DTO\Shoptet\Oauth\OauthResponse;
use App\DTO\Shoptet\Order\Order;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoice;
use App\DTO\Shoptet\WebhookRegistrationRequest;
use App\DTO\Shoptet\Webhooks\WebhookCreatedResponse;
use Nette\Http\Url;

interface ClientInterface
{
	public function confirmInstallation(string $code): ConfirmInstallation;

	public function registerWebHooks(WebhookRegistrationRequest $registrationRequest, Project $project): WebhookCreatedResponse;

	public function getEshopInfo(Project $project): void;

	public function getOauthAccessToken(string $code, Url $shopUrl): AccessToken;

	public function getEshopInfoFromAccessToken(AccessToken $accessToken, Url $shopUrl): OauthResponse;

	public function getClientId(): string;

	public function findProformaInvoice(string $code, Project $project): ProformaInvoice;

	public function findInvoice(string $code, Project $project): Invoice;

	public function findCreditNote(string $code, Project $project): CreditNote;

	public function findOrder(string $code, Project $project): Order;
}
