<?php

declare(strict_types=1);


namespace App\MessageBus\Handler\Synchronization;

use App\Manager\ProjectManager;
use App\MessageBus\Message\Synchronization\InvoiceSynchronizationMessage;
use App\Synchronization\InvoiceSynchronization;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SynchronizeInvoiceHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProjectManager $projectManager,
		private InvoiceSynchronization $synchronization
	) {
	}

	public function __invoke(InvoiceSynchronizationMessage $message): void
	{
		$project = $this->projectManager->getByEshopId($message->getEshopId());
		$this->synchronization->synchronize($project, $message->getDateTimeImmutable());
	}
}
