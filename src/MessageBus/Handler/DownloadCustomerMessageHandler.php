<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Customer;
use App\Savers\Shoptet\CustomerSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadCustomerMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private CustomerSaver $saver
	) {
	}

	public function __invoke(Customer $customer): void
	{
		dump(get_class($customer));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($customer->getEshopId());
		$creditNoteData = $this->client->findCustomer(
			$customer->getEventInstance(),
			$project
		);
		$this->saver->save($project, $creditNoteData);
	}
}
