<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\DTO\Shoptet\Request\Webhook;
use App\Manager\ProjectManager;
use App\MessageBus\Message\CreditNote;
use App\Savers\CreditNoteSaver;

class DownloadCreditNoteMessageHandler
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private CreditNoteSaver $saver
	) {
	}

	public function __invoke(CreditNote $creditNote): void
	{
		$project = $this->projectManager->getByEshopId($creditNote->getEshopId());
		switch ($creditNote->getEventType()) {
			case Webhook::TYPE_CREDIT_NOTE_CREATE:
			case Webhook::TYPE_CREDIT_NOTE_UPDATE:
				$creditNoteData = $this->client->findCreditNote(
					$creditNote->getEventInstance(),
					$project
				);
				$this->saver->save($project, $creditNoteData);
				break;
			case Webhook::TYPE_CREDIT_NOTE_DELETE:
				//todo delete
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
