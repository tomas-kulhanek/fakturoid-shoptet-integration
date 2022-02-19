<?php

declare(strict_types=1);

namespace App\MessageBus\Handler\Synchronization;

use App\Manager\ProjectManager;
use App\MessageBus\Message\Synchronization\CreditNoteSynchronizationMessage;
use App\Synchronization\CreditNoteSynchronization;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SynchronizeCreditNoteHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProjectManager            $projectManager,
		private CreditNoteSynchronization $synchronization
	) {
	}

	public function __invoke(CreditNoteSynchronizationMessage $message): void
	{
		$project = $this->projectManager->getByEshopId($message->getEshopId());
		$this->synchronization->synchronize($project, $message->getDateTimeImmutable());
	}
}
