<?php

declare(strict_types=1);

namespace App\Command\Accounting;

use App\Manager\AccountingManager;
use App\Manager\ProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tracy\Debugger;
use Tracy\ILogger;

class NumberLinesSynchronizeCommand extends Command
{
	/** @var ?string */
	protected static $defaultName = 'accounting:synchronize:numberLines';

	public function __construct(
		private ProjectManager    $projectManager,
		private AccountingManager $accountingManager
	) {
		parent::__construct(null);
	}

	protected function configure(): void
	{
		parent::configure();
		$this
			->setName(static::$defaultName)
			->addArgument('eshop', InputArgument::OPTIONAL)
			->addOption('startDate', 'd', InputOption::VALUE_OPTIONAL, 'From which date you want start')
			->setDescription('Synchronize credit notes for eshop');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$eshop = $input->getArgument('eshop');
		if ((string)intval($eshop) === $eshop) {
			$project = $this->projectManager->getByEshopId((int)$eshop);
			$projects = [$project];
		} elseif ($eshop !== null && $eshop !== '') {
			$project = $this->projectManager->getByEshopUrl($eshop);
			$projects = [$project];
		} else {
			$projects = $this->projectManager->getAllActiveProjects();
		}

		foreach ($projects as $project) {
			if (!$project->isActive()) {
				return Command::INVALID;
			}

			try {
				$this->accountingManager->syncNumberLines($project);
			} catch (\Exception $exception) {
				Debugger::log(sprintf('Error for project %s with %s', $project->getEshopId(), $exception->getMessage()), ILogger::CRITICAL);
			}
		}

		return 0;
	}
}
