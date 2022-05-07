<?php

declare(strict_types=1);


namespace App\MessageBus\Handler\Synchronization;

use App\Manager\ProjectManager;
use App\MessageBus\Message\Synchronization\OrderSynchronizationMessage;
use App\Synchronization\OrderSynchronization;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SynchronizeOrderHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProjectManager       $projectManager,
		private OrderSynchronization $synchronization
	)
	{
	}

	public function __invoke(OrderSynchronizationMessage $message): void
	{
		$project = $this->projectManager->getByEshopId($message->getEshopId());
		$this->synchronization->synchronize($project, $message->getDateTimeImmutable());
	}
}
