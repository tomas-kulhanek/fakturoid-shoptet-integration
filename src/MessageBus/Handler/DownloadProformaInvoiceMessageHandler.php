<?php

declare(strict_types=1);


namespace App\MessageBus\Handler;

use App\Api\ClientInterface;
use App\Database\EntityManager;
use App\DTO\Shoptet\Request\Webhook;
use App\Log\ActionLog;
use App\Manager\ProformaInvoiceManager;
use App\Manager\ProjectManager;
use App\MessageBus\Message\ProformaInvoice;
use App\Savers\ProformaInvoiceSaver;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DownloadProformaInvoiceMessageHandler implements MessageHandlerInterface
{
	public function __construct(
		private ClientInterface $client,
		private ProjectManager $projectManager,
		private ProformaInvoiceSaver $saver,
		private ProformaInvoiceManager $proformaInvoiceManager,
		private EntityManager $entityManager,
		private ActionLog $actionLog
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
				$proformaInvoice = $this->client->findProformaInvoice(
					$proformaInvoice->getEventInstance(),
					$project
				);
				$proformaInvoice = $this->saver->save($project, $proformaInvoice);
				$this->actionLog->log($project, ActionLog::SHOPTET_PROFORMA_DETAIL, $proformaInvoice->getId());
				break;
			case Webhook::TYPE_PROFORMA_INVOICE_DELETE:
				$proformaInvoice = $this->proformaInvoiceManager->findByShoptet($project, $proformaInvoice->getEventInstance());
				$proformaInvoice->setDeletedAt(new \DateTimeImmutable());
				$this->entityManager->flush($proformaInvoice);
				break;
			default:
				throw new \Exception('Unsupported type');
		}
	}
}
