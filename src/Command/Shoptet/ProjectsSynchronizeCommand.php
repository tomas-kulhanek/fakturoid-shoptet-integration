<?php

declare(strict_types=1);

namespace App\Command\Shoptet;

use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Manager\EshopInfoManager;
use App\Manager\ProjectManager;
use App\Synchronization\CreditNoteSynchronization;
use App\Synchronization\InvoiceSynchronization;
use App\Synchronization\OrderSynchronization;
use App\Synchronization\ProformaInvoiceSynchronization;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Tracy\Debugger;
use Tracy\ILogger;

class ProjectsSynchronizeCommand extends Command
{
	/** @var ?string */
	protected static $defaultName = 'shoptet:synchronize:projects';

	public function __construct(
		private EntityManager                  $entityManager,
		private ProjectManager                 $projectManager,
		private OrderSynchronization           $orderSynchronization,
		private ProformaInvoiceSynchronization $proformaInvoiceSynchronization,
		private InvoiceSynchronization         $invoiceSynchronization,
		private CreditNoteSynchronization      $creditNoteSynchronization,
		private EshopInfoManager               $eshopInfoManager
	)
	{
		parent::__construct(NULL);
	}

	protected function configure(): void
	{
		parent::configure();
		$this
			->setName(static::$defaultName)
			->setDescription('Synchronize orders for eshop');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$allActiveProjects = $this->projectManager->getAllActiveProjects();

		foreach ($allActiveProjects as $project) {
			try {
				$output->writeln(sprintf('Synchronize eshop %s', $project->getEshopHost()));
				$eshopId = $project->getEshopId();
				$project = $this->projectManager->getByEshopId($eshopId);
				$this->eshopInfoManager->syncCurrency($project);
				$project = $this->projectManager->getByEshopId($eshopId);
				$this->synchronizeOrders($project, $input, $output);
				$project = $this->projectManager->getByEshopId($eshopId);
				$this->synchronizeProformas($project, $input, $output);
				$project = $this->projectManager->getByEshopId($eshopId);
				sleep(1);
				$this->synchronizeInvoices($project, $input, $output);
				$project = $this->projectManager->getByEshopId($eshopId);
				sleep(1);
				$this->synchronizeCreditNotes($project, $input, $output);
			} catch (\Exception $exception) {
				Debugger::log($exception, ILogger::EXCEPTION);
			}
		}
		return Command::SUCCESS;
	}

	private function synchronizeOrders(Project $project, InputInterface $input, OutputInterface $output): void
	{
		if (!$project->getSettings()->isShoptetSynchronizeOrders()) {
			return;
		}
		$stopwatch = new Stopwatch();
		$stopwatch->start('synchronize');
		$output->writeln(sprintf('Start sync for eshop %s from %s', $project->getEshopHost(), $project->getLastOrderSyncAt()->format(DATE_ATOM)));
		$startAt = new \DateTimeImmutable();
		$totalSynchronized = $this->orderSynchronization->synchronize($project, $project->getLastOrderSyncAt());
		$project->setLastOrderSyncAt($startAt);
		$this->entityManager->flush();

		$event = $stopwatch->stop('synchronize');
		$output->writeln('');
		$output->writeln(sprintf('Completely we synchronize %d orders', $totalSynchronized));
		$output->writeln((string)$event);
	}

	private function synchronizeProformas(Project $project, InputInterface $input, OutputInterface $output): void
	{
		if (!$project->getSettings()->isShoptetSynchronizeProformaInvoices()) {
			return;
		}
		$stopwatch = new Stopwatch();
		$stopwatch->start('synchronize');
		$output->writeln(sprintf('Start sync for eshop %s from %s', $project->getEshopHost(), $project->getLastProformaSyncAt()->format(DATE_ATOM)));
		$startAt = new \DateTimeImmutable();
		$totalSynchronized = $this->proformaInvoiceSynchronization->synchronize($project, $project->getLastProformaSyncAt());
		$project->setLastProformaSyncAt($startAt);
		$this->entityManager->flush();

		$event = $stopwatch->stop('synchronize');
		$output->writeln('');
		$output->writeln(sprintf('Completely we synchronize %d proformas', $totalSynchronized));
		$output->writeln((string)$event);
	}

	private function synchronizeInvoices(Project $project, InputInterface $input, OutputInterface $output): void
	{
		if (!$project->getSettings()->isShoptetSynchronizeInvoices()) {
			return;
		}
		$stopwatch = new Stopwatch();
		$stopwatch->start('synchronize');
		$output->writeln(sprintf('Start sync for eshop %s from %s', $project->getEshopHost(), $project->getLastInvoiceSyncAt()->format(DATE_ATOM)));
		$startAt = new \DateTimeImmutable();
		$totalSynchronized = $this->invoiceSynchronization->synchronize($project, $project->getLastInvoiceSyncAt());
		$project->setLastInvoiceSyncAt($startAt);
		$this->entityManager->flush();

		$event = $stopwatch->stop('synchronize');
		$output->writeln('');
		$output->writeln(sprintf('Completely we synchronize %d invoices', $totalSynchronized));
		$output->writeln((string)$event);
	}

	private function synchronizeCreditNotes(Project $project, InputInterface $input, OutputInterface $output): void
	{
		if (!$project->getSettings()->isShoptetSynchronizeCreditNotes()) {
			return;
		}

		$stopwatch = new Stopwatch();
		$stopwatch->start('synchronize');
		$output->writeln(sprintf('Start sync for eshop %s from %s', $project->getEshopHost(), $project->getLastCreditNoteSyncAt()->format(DATE_ATOM)));
		$startAt = new \DateTimeImmutable();
		$totalSynchronized = $this->creditNoteSynchronization->synchronize($project, $project->getLastInvoiceSyncAt());
		$project->setLastCreditNoteSyncAt($startAt);
		$this->entityManager->flush();

		$event = $stopwatch->stop('synchronize');
		$output->writeln('');
		$output->writeln(sprintf('Completely we synchronize %d credit notes', $totalSynchronized));
		$output->writeln((string)$event);
	}
}
