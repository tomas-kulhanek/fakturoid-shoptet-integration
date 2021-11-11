<?php

namespace App\Modules\App\Invoice\Component\Grid;

use App\Components\DataGridComponent\DataGridFactory;
use App\Latte\NumberFormatter;
use App\Manager\InvoiceManager;
use App\MessageBus\AccountingBusDispatcher;
use App\Security\SecurityUser;
use Doctrine\ORM\QueryBuilder;
use Nette\Localization\Translator;

class InvoiceGridFactory
{
	public function __construct(
		private DataGridFactory         $dataGridFactory,
		private InvoiceManager          $invoiceManager,
		private SecurityUser            $securityUser,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private NumberFormatter         $numberFormatter,
		private Translator              $translator
	) {
	}

	public function create(QueryBuilder $dataSource): InvoiceGrid
	{
		return new InvoiceGrid(
			$this->dataGridFactory,
			$this->invoiceManager,
			$this->securityUser,
			$this->accountingBusDispatcher,
			$this->numberFormatter,
			$this->translator,
			$dataSource
		);
	}
}
