<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\Database\Entity\ProjectSetting;
use App\Database\EntityManager;
use App\DTO\Shoptet\ProformaInvoice\ProformaInvoiceResponse;
use App\DTO\Shoptet\Request\Webhook;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Log\ActionLog;
use App\Manager\ProformaInvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\AccountingBusDispatcher;
use App\MessageBus\Message\ProformaInvoice;
use App\Savers\ProformaInvoiceSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadProformaInvoiceMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface           $client,
		private ProjectManager            $projectManager,
		private ProformaInvoiceSaver      $saver,
		private ProformaInvoiceManager    $proformaInvoiceManager,
		private EntityManager             $entityManager,
		private ActionLog                 $actionLog,
		private Fakturoid\ProformaInvoice $proformaInvoice,
		private AccountingBusDispatcher   $accountingBusDispatcher
	) {
	}

	public function __invoke(ProformaInvoice $proformaInvoice): void
	{
		dump(get_class($proformaInvoice));
		dump(get_class($this));
		$project = $this->projectManager->getByEshopId($proformaInvoice->getEshopId());
		switch ($proformaInvoice->getEventType()) {
			case Webhook::TYPE_PROFORMA_INVOICE_CREATE:
			case Webhook::TYPE_PROFORMA_INVOICE_UPDATE:
				$proformaInvoiceData = $this->client->findProformaInvoice(
					$proformaInvoice->getEventInstance(),
					$project
				);
				if (!$proformaInvoiceData->hasErrors() && $proformaInvoiceData->data instanceof ProformaInvoiceResponse) {
					$proformaInvoice = $this->saver->save($project, $proformaInvoiceData->data->proformaInvoice);
					$this->actionLog->logProformaInvoice($project, ActionLog::SHOPTET_PROFORMA_DETAIL, $proformaInvoice);

					if ($proformaInvoice->getProject()->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) {
						$this->accountingBusDispatcher->dispatch($proformaInvoice);
					}
				}
				break;
			case Webhook::TYPE_PROFORMA_INVOICE_DELETE:
				$proformaInvoice = $this->proformaInvoiceManager->findByShoptet($project, $proformaInvoice->getEventInstance());
				$proformaInvoice->setDeletedAt(new \DateTimeImmutable());

				if ($proformaInvoice->getProject()->getSettings()->getAutomatization() === ProjectSetting::AUTOMATIZATION_AUTO) { //todo asi bych to hodil taky do redisu
					if ($proformaInvoice->getAccountingId() !== null) {
						$this->proformaInvoice->cancel($proformaInvoice);
					}
				}
				$this->entityManager->flush($proformaInvoice);
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
