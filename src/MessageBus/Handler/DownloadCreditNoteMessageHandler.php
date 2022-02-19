<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\DTO\Shoptet\CreditNote\CreditNoteResponse;
use App\DTO\Shoptet\Request\Webhook;
use App\Manager\ProjectManager;
use App\MessageBus\Message\CreditNote;
use App\Savers\CreditNoteSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadCreditNoteMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager  $projectManager,
		private CreditNoteSaver $saver
	) {
	}

	public function __invoke(CreditNote $creditNote): void
	{
		dump($creditNote::class);
		dump($this::class);
		$project = $this->projectManager->getByEshopId($creditNote->getEshopId());
		switch ($creditNote->getEventType()) {
			case Webhook::TYPE_CREDIT_NOTE_CREATE:
			case Webhook::TYPE_CREDIT_NOTE_UPDATE:
				$creditNoteData = $this->client->findCreditNote(
					$creditNote->getEventInstance(),
					$project
				);
				if (!$creditNoteData->hasErrors() && $creditNoteData->data instanceof CreditNoteResponse) {
					$this->saver->save($project, $creditNoteData->data->creditNote);
				}
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
