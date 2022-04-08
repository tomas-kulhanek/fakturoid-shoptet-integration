<?php

namespace App\Modules\App\Invoice\Component\Grid;

use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\Invoice;
use App\Exception\FakturoidException;
use App\Latte\NumberFormatter;
use App\Manager\InvoiceManager;
use App\MessageBus\AccountingBusDispatcher;
use App\Modules\Base\BasePresenter;
use App\Security\SecurityUser;
use Doctrine\ORM\QueryBuilder;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Localization\Translator;
use Nette\Utils\Html;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method BasePresenter getPresenter()
 * @method DefaultTemplate getTemplate()
 */
class InvoiceGrid extends Control
{
	public function __construct(
		private DataGridFactory         $dataGridFactory,
		private InvoiceManager          $invoiceManager,
		private SecurityUser            $securityUser,
		private AccountingBusDispatcher $accountingBusDispatcher,
		private NumberFormatter         $numberFormatter,
		private Translator              $translator,
		private QueryBuilder            $dataSource
	) {
	}

	protected function createComponentPageGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'desc']);
		$grid->setDataSource(
			$this->dataSource
		);

		$grid->addColumnText('code', '#')
			->setSortable();
		$grid->addColumnDateTime('creationTime', 'messages.invoiceList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnText('orderCode', 'messages.invoiceList.column.orderCode')
			->setSortable();
		$grid->addColumnText('proformaInvoiceCode', 'messages.invoiceList.column.proformaInvoiceCode')
			->setSortable()
			->setDefaultHide(true);
		$grid->addColumnText('accountingNumber', 'messages.invoiceList.column.accountingNumber')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.invoiceList.column.changeTime')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addColumnDateTime('accountingUpdatedAt', 'messages.invoiceList.column.accountingUpdatedAt')
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

		$grid->addGroupAction(
			'messages.invoiceList.uploadToAccounting'
		)->onSelect[] = function (array $ids): void {
			$results = ['success' => [], 'error' => []];
			foreach ($ids as $id) {
				$invoice = $this->invoiceManager->find($this->securityUser->getProjectEntity(), $id);
				bdump($invoice);
				try {
					$this->accountingBusDispatcher->dispatch($invoice);
					$results['success'][] = $invoice->getCode();
				} catch (FakturoidException $exception) {
					Debugger::log($exception);
					$this->getPresenter()->flashError($invoice->getCode() . ' - ' . $exception->humanize());
				}
			}
			if (count($results['success']) > 0) {
				$this->getPresenter()->flashSuccess(
					$this->translator->translate(
						'messages.invoiceList.message.massUploadToAccounting.success',
						[
							'codes' => implode(', ', $results['success']),
						]
					)
				);
			}
			if ($this->getPresenter()->isAjax()) {
				$this->getPresenter()->redrawControl('flashes');
				$this->getPresenter()->redrawControl();
				$this['pageGrid']->redrawControl();
			}
		};

		$grid->addAction('accounting', '')
			->setRenderCondition(fn (Document $document) => $document->getAccountingPublicHtmlUrl() !== null && !$document->isDeleted())
			->setRenderer(function (Document $document): Html {
				$link = Html::el('a');

				return $link->href($document->getAccountingPublicHtmlUrl())
					->class('btn btn-xs btn-success')
					->target('_blank')
					->addHtml(
						Html::el('span')
							->class('fa fa-file-invoice')
					);
			});

		$grid->setRowCallback(function (Document $document, Html $tr): void {
			if ($document->isDeleted()) {
				$tr->addClass('bg-danger disabled');

				return;
			}
			if ($document->isAccountingError()) {
				$tr->addClass('bg-danger');
				$tr->title($document->getAccountingLastError());
			}
		});
		$grid->allowRowsGroupAction(fn (Document $document) => !$document->isDeleted());

		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setRenderCondition(fn (Document $document) => $document->getShoptetCode() !== null && $document->getShoptetCode() !== '' && !$document->isDeleted())
			->setConfirmation(
				new CallbackConfirmation(
					fn (Document $item): string => $this->translator->translate('messages.invoiceList.synchronizeQuestion', ['code' => $item->getCode()])
				)
			);
		//$grid->addActionCallback('detail', '', fn (string $id) => $this->getPresenter()->redirect(':App:Invoice:detail', ['id' => $id]))
		//	->setRenderCondition(fn (Document $document) => !$document->isDeleted())
		//	->setIcon('eye')
		//	->setClass('btn btn-xs btn-primary');

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


	public function handleSynchronize(int $id): void
	{
		/** @var Invoice $entity */
		$entity = $this->invoiceManager->find($this->securityUser->getProjectEntity(), $id);
		try {
			$entity = $this->invoiceManager->synchronizeFromShoptet($this->securityUser->getProjectEntity(), $entity->getShoptetCode());
			$this->redrawControl('orderDetail');
			$this->getPresenter()->flashSuccess($this->translator->translate('messages.invoiceList.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->getPresenter()->flashError($this->translator->translate('messages.invoiceList.message.synchronize.error', ['code' => $entity->getCode()]));
		}
		if ($this->getPresenter()->isAjax()) {
			$this->getPresenter()->redrawControl('flashes');
			$this['pageGrid']->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}

	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/default.latte');

		$this->getTemplate()->render();
	}
}
