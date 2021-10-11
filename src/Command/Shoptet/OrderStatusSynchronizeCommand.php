<?php

declare(strict_types=1);

namespace App\Command\Shoptet;

use App\Manager\EshopInfoManager;
use App\Manager\ProjectManager;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OrderStatusSynchronizeCommand extends ProjectCommand
{
	/** @var string */
	protected static $defaultName = 'shoptet:synchronize:order-status';

	public function __construct(
		\App\Manager\Core\ProjectManager $coreProjectManager,
		Connection                       $connection,
		private ProjectManager           $projectManager,
		private EshopInfoManager         $eshopInfoManager
	)
	{
		parent::__construct($coreProjectManager, $connection);
	}


	protected function configure(): void
	{
		parent::configure();
		$this
			->setName(static::$defaultName)
			->setDescription('Synchronize customers for eshop');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$parentResult = parent::execute($input, $output);
		if ($parentResult !== Command::SUCCESS) {
			return $parentResult;
		}
		$eshop = $input->getArgument('eshop');
		if ((string) intval($eshop) === $eshop) {
			$project = $this->projectManager->getByEshopId((int) $eshop);
		} else {
			$project = $this->projectManager->getByEshopUrl($eshop);
		}
		$output->writeln($project->getEshopHost());
		if (!$project->isActive()) {
			return Command::INVALID;
		}

		$this->eshopInfoManager->syncOrderStatuses($project);

		return Command::SUCCESS;
	}
}
