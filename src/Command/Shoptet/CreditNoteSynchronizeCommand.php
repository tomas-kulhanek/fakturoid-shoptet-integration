<?php

declare(strict_types=1);

namespace App\Command\Shoptet;

use App\Database\EntityManager;
use App\Manager\ProjectManager;
use App\Synchronization\CreditNoteSynchronization;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Stopwatch\Stopwatch;

class CreditNoteSynchronizeCommand extends Command
{
	/** @var ?string */
	protected static $defaultName = 'shoptet:synchronize:creditNote';

	public function __construct(
		private EntityManager             $entityManager,
		private ProjectManager            $projectManager,
		private CreditNoteSynchronization $creditNoteSynchronization
	)
	{
		parent::__construct(NULL);
	}

	protected function configure(): void
	{
		parent::configure();
		$this
			->setName(static::$defaultName)
			->addArgument('eshop', InputArgument::REQUIRED)
			->addOption('startDate', 'd', InputOption::VALUE_OPTIONAL, 'From which date you want start')
			->setDescription('Synchronize credit notes for eshop');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$eshop = $input->getArgument('eshop');
		if ((string)intval($eshop) === $eshop) {
			$project = $this->projectManager->getByEshopId((int)$eshop);
		} else {
			$project = $this->projectManager->getByEshopUrl($eshop);
		}
		if (!$project->isActive()) {
			return Command::INVALID;
		}
		$loadFrom = $project->getLastCreditNoteSyncAt();
		if ($input->getOption('startDate') !== NULL) {
			$loadFrom = \DateTimeImmutable::createFromFormat('Y-m-d', $input->getOption('startDate'));
			if ($loadFrom === FALSE) {
				$helper = $this->getHelper('question');
				$question = new ConfirmationQuestion(sprintf('Zadane datum neni validni, chcete pokracovat od %s? [y/N]', $project->getLastCreditNoteSyncAt()->format('Y-m-d H:i')), FALSE);
				if (!$helper->ask($input, $output, $question)) {
					return Command::SUCCESS;
				}
				$loadFrom = $project->getLastCreditNoteSyncAt();
			}
			$loadFrom = $loadFrom->setTime(0, 0, 0);
		}
		$stopwatch = new Stopwatch();
		$stopwatch->start('synchronize');

		$startAt = new \DateTimeImmutable();
		$totalSynchronized = $this->creditNoteSynchronization->synchronize($project, $loadFrom);


		$eshop = $input->getArgument('eshop');
		if ((string)intval($eshop) === $eshop) {
			$project = $this->projectManager->getByEshopId((int)$eshop);
		} else {
			$project = $this->projectManager->getByEshopUrl($eshop);
		}
		$project->setLastCreditNoteSyncAt($startAt);
		$this->entityManager->flush();

		$event = $stopwatch->stop('synchronize');
		$output->writeln('');
		$output->writeln(sprintf('Completely we synchronize %d credit notes', $totalSynchronized));
		$output->writeln((string)$event);

		return 0;
	}
}
