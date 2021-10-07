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
	public function getMaxClientTokens(): int
	{
		return 1;
	}
}
