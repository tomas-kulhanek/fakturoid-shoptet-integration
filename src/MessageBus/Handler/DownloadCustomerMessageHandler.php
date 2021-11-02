<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\DTO\Shoptet\Customer\CustomerResponse;
use App\Log\ActionLog;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Customer;
use App\Savers\Shoptet\CustomerSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadCustomerMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private CustomerSaver $saver,
		private ActionLog $actionLog
	) {
	}

	public function __invoke(Customer $customer): void
	{
		dump(get_class($customer));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($customer->getEshopId());
		$customerResponse = $this->client->findCustomer(
			$customer->getEventInstance(),
			$project
		);
		if (!$customerResponse->data instanceof CustomerResponse) {
			return;
		}
		$customer = $this->saver->save($project, $customerResponse->data->customer);
		$this->actionLog->log($project, ActionLog::SHOPTET_CUSTOMER_DETAIL, $customer->getId());
	}
}
