<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\Manager\ProjectManager;
use App\MessageBus\Message\CreditNote;
use App\MessageBus\Message\Customer;
use App\Savers\Shoptet\CustomerSaver;

class DownloadCustomerMessageHandler
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager  $projectManager,
		private CustomerSaver   $saver
	) {
	}

	public function __invoke(Customer $customer): void
	{
		$project = $this->projectManager->getByEshopId($customer->getEshopId());
		$creditNoteData = $this->client->findCustomer(
			$customer->getEventInstance(),
			$project
		);
		$this->saver->save($project, $creditNoteData);
	}
}
