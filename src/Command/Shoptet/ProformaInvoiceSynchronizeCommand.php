<?php

declare(strict_types=1);

namespace App\Command\Shoptet;

use App\Database\EntityManager;
use App\Synchronization\ProformaInvoiceSynchronization;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Stopwatch\Stopwatch;

class ProformaInvoiceSynchronizeCommand extends Command
{
	/** @var string */
	protected static $defaultName = 'shoptet:synchronize:proformainvoice';

	public function __construct(
		private EntityManager                  $entityManager,
		private \App\Manager\ProjectManager    $projectManager,
		private ProformaInvoiceSynchronization $invoiceSynchronization
	) {
		parent::__construct(null);
	}


	protected function configure(): void
	{
		parent::configure();
		$this
			->setName(static::$defaultName)
			->addArgument('eshop', InputArgument::REQUIRED)
			->addOption('startDate', 'd', InputOption::VALUE_OPTIONAL, 'From which date you want start')
			->setDescription('Synchronize invoices for eshop');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$eshop = $input->getArgument('eshop');
		if ((string) intval($eshop) === $eshop) {
			$project = $this->projectManager->getByEshopId((int) $eshop);
		} else {
			$project = $this->projectManager->getByEshopUrl($eshop);
		}
		if (!$project->isActive()) {
			return Command::INVALID;
		}
		$loadFrom = $project->getLastProformaSyncAt();
		if ($input->getOption('startDate') !== null) {
			$loadFrom = \DateTimeImmutable::createFromFormat('Y-m-d', $input->getOption('startDate'));
			if ($loadFrom === false) {
				$helper = $this->getHelper('question');
				$question = new ConfirmationQuestion(sprintf('Zadane datum neni validni, chcete pokracovat od %s? [y/N]', $project->getLastProformaSyncAt()->format('Y-m-d H:i')), false);
				if (!$helper->ask($input, $output, $question)) {
					return Command::SUCCESS;
				}
				$loadFrom = $project->getLastProformaSyncAt();
			}
			$loadFrom = $loadFrom->setTime(0, 0, 0);
		}
		$stopwatch = new Stopwatch();
		$stopwatch->start('synchronize');

		$startAt = new \DateTimeImmutable();
		$totalSynchronized = $this->invoiceSynchronization->synchronize($project, $loadFrom);
		$project->setLastOrderSyncAt($startAt);
		$this->entityManager->flush($project);

		$event = $stopwatch->stop('synchronize');
		$output->writeln('');
		$output->writeln(sprintf('Completely we synchronize %d invoices', $totalSynchronized));
		$output->writeln((string) $event);

		return 0;
	}
}
