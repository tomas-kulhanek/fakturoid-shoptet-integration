<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\Invoice;

use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\Invoice;
use App\Facade\Fakturoid\CreateInvoice;
use App\Latte\NumberFormatter;
use App\Manager\InvoiceManager;
use App\Modules\Shoptet\BaseShoptetPresenter;
use App\Security\SecurityUser;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;
use Nette\Localization\Translator;
use Nette\Utils\Html;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class InvoicePresenter extends BaseShoptetPresenter
{
	#[Inject]
	public NumberFormatter $numberFormatter;

	public function __construct(
		private DataGridFactory $dataGridFactory,
		private CreateInvoice   $createInvoiceAccounting,
		private InvoiceManager  $invoiceManager
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('Shoptet:Invoice')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	public function handleSynchronize(int $id): void //todo jen v nekterych pripadech!!!
	{
		/** @var Invoice $entity */
		$entity = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		try {
			$entity = $this->invoiceManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $entity->getShoptetCode());
			$this->redrawControl('orderDetail');
			$this->flashSuccess($this->getTranslator()->translate('messages.invoiceList.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->getTranslator()->translate('messages.invoiceList.message.synchronize.error', ['code' => $entity->getCode()]));
		}
		if ($this->isAjax()) {
			$this->redrawControl('flashes');
			$this['orderGrid']->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('pageDetail');
		}
		$entity = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		;
		bdump($entity);
		$this->getTemplate()->setParameters([
			'invoice' => $entity,
		]);
	}

	public function handleCreateInAccounting(int $id): void
	{
		$invoice = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		bdump($invoice);
		if ($invoice->getAccountingSubjectId() === null) {
			$this->createInvoiceAccounting->create(invoice: $invoice);
			$this->flashSuccess(
				$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.success')
			);
		} else {
			$this->flashWarning(
				$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.alreadyExists')
			);
		}
		$this->redirect('detail', ['id' => $id]);
	}

	protected function createComponentPageGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->invoiceManager->getRepository()->createQueryBuilder('i')
				->addSelect('id')
				->addSelect('ib')
				->leftJoin('i.deliveryAddress', 'id')
				->leftJoin('i.billingAddress', 'ib')
				->where('i.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);

		$grid->addColumnText('isValid', '')
			->setRenderer(function (Invoice $invoice): Html {
				if ($invoice->isValid()) {
					return
						Html::el('i')
							->class('fa fa-check-circle text-success');
				}
				return
					Html::el('i')
						->class('text-danger fa fa-times-circle');
			});
		$grid->addColumnText('code', '#')
			->setSortable();
		$grid->addColumnDateTime('creationTime', 'messages.invoiceList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnText('orderCode', 'messages.invoiceList.column.orderCode')
			->setSortable();
		$grid->addColumnText('proformaInvoiceCode', 'messages.invoiceList.column.proformaInvoiceCode')
			->setSortable();
		$grid->addColumnText('accountingNumber', 'messages.invoiceList.column.accountingNumber')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.invoiceList.column.changeTime')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addColumnDateTime('dueDate', 'messages.invoiceList.column.dueDate')
			->setFormat('d.m.Y')
			->setSortable();
		$grid->addColumnDateTime('taxDate', 'messages.invoiceList.column.taxDate')
			->setFormat('d.m.Y')
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'messages.invoiceList.column.billingFullName')
			->setSortable();
		$grid->addColumnNumber('withVat', 'messages.invoiceList.column.withVat', 'mainWithVat')
			->setSortable()
			->setRenderer(fn (Document $order) => $this->numberFormatter->__invoke($order->getWithVat(), $order->getCurrencyCode()));
		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addAction('sync', '', 'synchronize!')
		->setIcon('sync')
			->setRenderCondition(fn (Document$document) => $document->getShoptetCode() !== null && $document->getShoptetCode() !== '')
			->setConfirmation(
				new CallbackConfirmation(
					function (Invoice $item) use ($presenter): string {
						return $presenter->translator->translate('messages.invoiceList.synchronizeQuestion', ['code' => $item->getCode()]);
					}
				)
			);


		$grid->addFilterDateRange('creationTime', 'messages.invoiceList.column.creationTime');

		$grid->addFilterText('orderCode', 'messages.invoiceList.column.orderCode');
		$grid->addFilterText('proformaInvoiceCode', 'messages.invoiceList.column.proformaInvoiceCode');

		$grid->addFilterDateRange('changeTime', 'messages.invoiceList.column.changeTime');
		$grid->addFilterDateRange('dueDate', 'messages.invoiceList.column.dueDate');
		$grid->addFilterDateRange('taxDate', 'messages.invoiceList.column.taxDate');

		$grid->cantSetHiddenColumn('isValid');
		$grid->cantSetHiddenColumn('code');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
