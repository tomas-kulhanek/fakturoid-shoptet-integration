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
		return '470424-a-716-1wts0sw5c1oi3krjopadyzvbab8qjdel';
	}
}
