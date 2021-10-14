<?php

declare(strict_types=1);


namespace App\MessageBus\Handler\Synchronization;

use App\Manager\ProjectManager;
use App\MessageBus\Message\Synchronization\ProformaInvoiceSynchronizationMessage;
use App\Synchronization\ProformaInvoiceSynchronization;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SynchronizeProformaInvoiceHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProjectManager $projectManager,
		private ProformaInvoiceSynchronization $synchronization
	) {
	}

	public function __invoke(ProformaInvoiceSynchronizationMessage $message): void
	{
		$project = $this->projectManager->getByEshopId($message->getEshopId());
		$this->synchronization->synchronize($project, $message->getDateTimeImmutable());
	}
}
