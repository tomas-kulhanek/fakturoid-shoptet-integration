<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\DTO\Shoptet\Request\Webhook;
use App\Log\ActionLog;
use App\Manager\ProjectManager;
use App\MessageBus\Message\CreditNote;
use App\Savers\CreditNoteSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadCreditNoteMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private CreditNoteSaver $saver,
		private ActionLog $actionLog
	) {
	}

	public function __invoke(CreditNote $creditNote): void
	{
		dump(get_class($creditNote));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($creditNote->getEshopId());
		switch ($creditNote->getEventType()) {
			case Webhook::TYPE_CREDIT_NOTE_CREATE:
			case Webhook::TYPE_CREDIT_NOTE_UPDATE:
				$creditNoteData = $this->client->findCreditNote(
					$creditNote->getEventInstance(),
					$project
				);
				$creditNote = $this->saver->save($project, $creditNoteData);

				$this->actionLog->log($project, ActionLog::SHOPTET_CREDIT_NOTE_DETAIL, $creditNote->getId());
				break;
			case Webhook::TYPE_CREDIT_NOTE_DELETE:

				//$invoiceEntity = $this->invoiceManager->findByShoptet($project, $invoice->getEventInstance());
				//$invoiceEntity->setDeletedAt(new \DateTimeImmutable());
				//$this->entityManager->flush($invoiceEntity);
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
