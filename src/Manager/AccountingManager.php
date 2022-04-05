<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\FakturoidFactory;
use App\Database\Entity\Shoptet\Project;
use App\Savers\Accounting\BankAccountSaver;
use App\Savers\Accounting\NumberLinesSaver;

class AccountingManager
{
	public function __construct(
		private FakturoidFactory $fakturoidFactory,
		private BankAccountSaver $bankAccountSaver,
		private NumberLinesSaver $numberLinesSaver
	) {
	}

	public function syncBankAccounts(Project $project): void
	{
		$bankAccounts = $this->fakturoidFactory->createClientFromSetting($project->getSettings())
			->getBankAccounts();
		$this->bankAccountSaver->save($project, $bankAccounts->getBody());
	}

	public function syncNumberLines(Project $project): void
	{
		$numberLines = $this->fakturoidFactory->createClientFromSetting($project->getSettings())
			->getNumberLines();
		$this->numberLinesSaver->save($project, $numberLines->getBody());
	}
}
