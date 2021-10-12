<?php

declare(strict_types=1);


namespace App\Command\Shoptet;

use App\DBAL\MultiDbConnectionWrapper;
use App\Manager\Core\ProjectManager;
use Doctrine\DBAL\Connection;
use Nette\Database\Row;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class ProjectCommand extends Command
{
	/**
	 * @param ProjectManager $coreProjectManager
	 * @param MultiDbConnectionWrapper $connection
	 */
	public function __construct(
		private ProjectManager             $coreProjectManager,
		private Connection                 $connection
	) {
		parent::__construct(null);
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$eshop = $input->getArgument('eshop');
		if ((string) intval($eshop) === $eshop) {
			$project = $this->coreProjectManager->getByEshopId((int) $eshop);
		} else {
			$project = $this->coreProjectManager->getByEshopUrl($eshop);
		}
		if (!$project instanceof Row) {
			return Command::FAILURE;
		}
		$this->connection->selectDatabase((int) $project->eshop_id);
		return Command::SUCCESS;
	}


	protected function configure(): void
	{
		parent::configure();
		$this
			->setName(static::$defaultName)
			->addArgument('eshop', InputArgument::REQUIRED);
	}
}