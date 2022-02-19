<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\CreditNote;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\CreditNoteRepository;
use App\DTO\Shoptet\CreditNote\CreditNoteResponse;
use App\Log\ActionLog;
use App\Savers\CreditNoteSaver;

class CreditNoteManager
{
	public function __construct(
		private EntityManager   $entityManager,
		private CreditNoteSaver $creditNoteSaver,
		private ClientInterface $shoptetClient,
		private ActionLog       $actionLog
	) {
	}

	public function getRepository(): CreditNoteRepository
	{
		/** @var CreditNoteRepository $repository */
		$repository = $this->entityManager->getRepository(CreditNote::class);
		return $repository;
	}

	public function synchronizeFromShoptet(Project $project, string $code): ?CreditNote
	{
		$orderData = $this->shoptetClient->findCreditNote($code, $project);
		bdump($orderData);
		if (!$orderData->data instanceof CreditNoteResponse) {
			return null;
		}
		$proformaInvoice = $this->creditNoteSaver->save($project, $orderData->data->creditNote);
		$this->actionLog->logCreditNote($project, ActionLog::SHOPTET_CREDIT_NOTE_DETAIL, $proformaInvoice);
		$this->entityManager->refresh($proformaInvoice);
		return $proformaInvoice;
	}

	public function find(Project $project, int $id): ?CreditNote
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'id' => $id]);
	}

	public function findByShoptet(Project $project, string $shoptetCode): ?CreditNote
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'shoptetCode' => $shoptetCode]);
	}
}
