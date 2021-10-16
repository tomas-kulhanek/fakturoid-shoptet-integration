<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\FakturoidFactory;
use App\Database\Entity\Shoptet\Project;
use App\Savers\Accounting\BankAccountSaver;

class AccountingManager
{
	public function __construct(
		private FakturoidFactory $fakturoidFactory,
		private BankAccountSaver $bankAccountSaver
	) {
	}

	public function syncBankAccounts(Project $project): void
	{
		$bankAccounts = $this->fakturoidFactory->createClientFromSetting($project->getSettings())
			->getBankAccounts();
		$this->bankAccountSaver->save($project, $bankAccounts->getBody());
	}
}
