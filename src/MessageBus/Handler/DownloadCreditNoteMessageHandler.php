<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Database\Entity\ProjectSetting;
use App\Database\EntityManager;
use App\DTO\Shoptet\Request\Webhook;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Manager\CreditNoteManager;
use App\Manager\ProjectManager;
use App\MessageBus\AccountingBusDispatcher;
use App\MessageBus\Message\CreditNote;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadCreditNoteMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ProjectManager          $projectManager,
		private CreditNoteManager       $creditNoteManager,
		private EntityManager           $entityManager,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private Fakturoid\CreditNote    $accounting,
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

				$invoiceEntity = $this->creditNoteManager->synchronizeFromShoptet($project, $creditNote->getEventInstance());
				if ($invoiceEntity === null) {
					throw new \Exception();
				}
				if ($project->getSettings()->getAutomatization() !== ProjectSetting::AUTOMATIZATION_AUTO) {
					break;
				}

				try {
					$this->accountingBusDispatcher->dispatch($invoiceEntity);
				} catch (EmptyLines|FakturoidException) {
					//silent
				}
				break;
			case Webhook::TYPE_CREDIT_NOTE_DELETE:
				$invoiceEntity = $this->creditNoteManager->findByShoptet($project, $creditNote->getEventInstance());
				if ($invoiceEntity instanceof \App\Database\Entity\Shoptet\CreditNote) {
					$invoiceEntity->setDeletedAt(new \DateTimeImmutable());

					if ($invoiceEntity->getProject()->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
						if ($invoiceEntity->getAccountingId() !== null) {
							$this->accounting->cancel($invoiceEntity);
						}
					}
					foreach ($invoiceEntity->getItems() as $item) {
						$this->entityManager->remove($item);
					}
					$this->entityManager->remove($invoiceEntity);
					$this->entityManager->flush();
				}
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
