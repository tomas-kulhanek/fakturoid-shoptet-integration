<?php

namespace App\Modules\App\CreditNote\Component\Grid;

use App\Components\DataGridComponent\DataGridFactory;
use App\Latte\NumberFormatter;
use App\Manager\CreditNoteManager;
use App\MessageBus\AccountingBusDispatcher;
use App\Security\SecurityUser;
use Doctrine\ORM\QueryBuilder;
use Nette\Localization\Translator;

class CreditNoteGridFactory
{
	public function __construct(
		private DataGridFactory         $dataGridFactory,
		private CreditNoteManager       $creditNoteManager,
		private SecurityUser            $securityUser,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private NumberFormatter         $numberFormatter,
		private Translator              $translator
	) {
	}

	public function create(QueryBuilder $dataSource): CreditNoteGrid
	{
		return new CreditNoteGrid(
			$this->dataGridFactory,
			$this->creditNoteManager,
			$this->securityUser,
			$this->accountingBusDispatcher,
			$this->numberFormatter,
			$this->translator,
			$dataSource
		);
	}
}
