<?php

declare(strict_types=1);


namespace App\MessageBus\Handler\Synchronization;

use App\Database\EntityManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\Synchronization\CustomerSynchronizationMessage;
use App\Synchronization\CustomerSynchronization;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SynchronizeCustomerHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProjectManager          $projectManager,
		private CustomerSynchronization $synchronization,
		private EntityManager           $entityManager
	) {
	}

	public function __invoke(CustomerSynchronizationMessage $message): void
	{
		$this->entityManager->clear();
		$project = $this->projectManager->getByEshopId($message->getEshopId());
		$this->synchronization->synchronize($project, $message->getDateTimeImmutable());
	}
}
