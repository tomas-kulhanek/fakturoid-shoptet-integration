<?php

declare(strict_types=1);

namespace App\Command\Shoptet;

use App\Manager\EshopInfoManager;
use App\Manager\ProjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurrencySynchronizeCommand extends Command
{
	/** @var ?string */
	protected static $defaultName = 'shoptet:synchronize:currency';

	public function __construct(
		private ProjectManager   $projectManager,
		private EshopInfoManager $eshopInfoManager
	) {
		parent::__construct(null);
	}


	protected function configure(): void
	{
		parent::configure();
		$this
			->setName(static::$defaultName)
			->addArgument('eshop', InputArgument::REQUIRED)
			->setDescription('Synchronize currencies for eshop');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$eshop = $input->getArgument('eshop');
		if ((string)intval($eshop) === $eshop) {
			$project = $this->projectManager->getByEshopId((int)$eshop);
		} else {
			$project = $this->projectManager->getByEshopUrl($eshop);
		}
		$output->writeln($project->getEshopHost());
		if (!$project->isActive()) {
			return Command::INVALID;
		}

		$this->eshopInfoManager->syncCurrency($project);

		return Command::SUCCESS;
	}
}
