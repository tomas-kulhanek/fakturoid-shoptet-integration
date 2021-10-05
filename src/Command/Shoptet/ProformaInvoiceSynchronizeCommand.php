<?php

declare(strict_types=1);

namespace App\Command\Shoptet;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Document;
use App\Database\EntityManager;
use App\DTO\Shoptet\ChangeResponse;
use App\Manager\ProformaInvoiceManager;
use App\Manager\ProjectManager;
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
		private EntityManager          $entityManager,
		private ProjectManager         $projectManager,
		private ProformaInvoiceManager $invoiceManager,
		private ClientInterface        $client
	) {
		parent::__construct(null);
	}

	protected function configure(): void
	{
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
		$totalSynchronized = 0;
		$stopwatch = new Stopwatch();
		$stopwatch->start('synchronize');


		$output->writeln(sprintf('Start sync for eshop %s from %s', $project->getEshopHost(), $loadFrom->format(DATE_ATOM)));
		$response = $this->client->getProformaInvoiceChanges($project, $loadFrom);

		$output->writeln(sprintf('- Found %d from total %d', $response->paginator->itemsOnPage, $response->paginator->totalCount));
		/** @var ChangeResponse $change */
		foreach ($response->changes as $change) {
			$entity = $this->invoiceManager->findByShoptet($project, $change->code);
			if ($entity instanceof Document) {
				if ($entity->getChangeTime() >= $change->changeTime) {
					$output->writeln('-- skip ' . $change->code);
					continue;
				}
			}
			$output->writeln('-- start synchronize ' . $change->code);
			$this->invoiceManager->synchronizeFromShoptet($project, $change->code);
			$output->writeln('-- end synchronize');
			$totalSynchronized++;
		}
		$total = $response->paginator->page * $response->paginator->itemsPerPage;

		while ($response->paginator->totalCount > $total) {
			$response = $this->client->getProformaInvoiceChanges($project, $loadFrom, ($response->paginator->page + 1));
			$output->writeln(sprintf('- [page %d] Found %d from total %d', $response->paginator->page, $response->paginator->itemsOnPage, $response->paginator->totalCount));
			/** @var ChangeResponse $change */
			foreach ($response->changes as $change) {
				$entity = $this->invoiceManager->findByShoptet($project, $change->code);
				if ($entity instanceof Document) {
					if ($entity->getChangeTime() >= $change->changeTime) {
						$output->writeln('-- skip ' . $change->code);
						continue;
					}
				}
				$output->writeln('-- start synchronize ' . $change->code);
				$this->invoiceManager->synchronizeFromShoptet($project, $change->code);
				$output->writeln('-- end synchronize');
				$totalSynchronized++;
			}
			$total = $response->paginator->page * $response->paginator->itemsPerPage;
		}
		$event = $stopwatch->stop('synchronize');
		$output->writeln('');
		$output->writeln(sprintf('Completely we synchronize %d invoices', $totalSynchronized));
		$output->writeln((string) $event);
		$project->setLastProformaSyncAt(new \DateTimeImmutable());
		$this->entityManager->flush($project);

		return 0;
	}
}
