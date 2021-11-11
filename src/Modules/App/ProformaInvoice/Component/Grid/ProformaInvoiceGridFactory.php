<?php

namespace App\Modules\App\ProformaInvoice\Component\Grid;

use App\Components\DataGridComponent\DataGridFactory;
use App\Latte\NumberFormatter;
use App\Manager\ProformaInvoiceManager;
use App\MessageBus\AccountingBusDispatcher;
use App\Security\SecurityUser;
use Doctrine\ORM\QueryBuilder;
use Nette\Localization\Translator;

class ProformaInvoiceGridFactory
{
	public function __construct(
		private DataGridFactory         $dataGridFactory,
		private ProformaInvoiceManager          $invoiceManager,
		private SecurityUser            $securityUser,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private NumberFormatter         $numberFormatter,
		private Translator              $translator
	) {
	}

	public function create(QueryBuilder $dataSource): ProformaInvoiceGrid
	{
		return new ProformaInvoiceGrid(
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
