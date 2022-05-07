<?php

namespace App\Modules\App\ProformaInvoice\Component\Grid;

use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Exception\FakturoidException;
use App\Latte\NumberFormatter;
use App\Manager\ProformaInvoiceManager;
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
class ProformaInvoiceGrid extends Control
{
	public function __construct(
		private DataGridFactory         $dataGridFactory,
		private ProformaInvoiceManager  $invoiceManager,
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
		$grid->setDataSource($this->dataSource);

		$grid->addColumnText('code', '#')
			->setSortable();
		$grid->addColumnDateTime('creationTime', 'messages.proformaInvoiceList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.proformaInvoiceList.column.changeTime')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addColumnDateTime('accountingUpdatedAt', 'messages.proformaInvoiceList.column.accountingUpdatedAt')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addColumnText('orderCode', 'messages.proformaInvoiceList.column.orderCode')
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'messages.proformaInvoiceList.column.billingFullName')
			->setSortable();
		$grid->addColumnNumber('withVat', 'messages.proformaInvoiceList.column.withVat')
			->setSortable()
			->setRenderer(fn (Document $order) => $this->numberFormatter->__invoke($order->getWithVat(), $order->getCurrencyCode()));

		$grid->addGroupAction(
			'messages.proformaInvoiceList.uploadToAccounting'
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
						'messages.proformaInvoiceList.message.massUploadToAccounting.success',
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

		$grid->setItemsDetail(__DIR__ . '/templates/grid.itemDetails.latte')
			->setRenderCondition(fn (Document $document) => !$document->isDeleted() && $document->isAccountingError());
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
					fn (Document $item): string => $this->translator->translate('messages.proformaInvoiceList.synchronizeQuestion', ['code' => $item->getCode()])
				)
			);
		//$grid->addActionCallback('detail', '', fn (string $id) => $this->getPresenter()->redirect(':App:ProformaInvoice:detail', ['id' => $id]))
		//	->setRenderCondition(fn (Document $document) => !$document->isDeleted())
		//	->setIcon('eye')
		//	->setClass('btn btn-xs btn-primary');

		$grid->addFilterDateRange('creationTime', 'messages.proformaInvoiceList.column.creationTime');

		$grid->addFilterText('orderCode', 'messages.proformaInvoiceList.column.orderCode');
		$grid->addFilterText('proformaInvoiceCode', 'messages.proformaInvoiceList.column.proformaInvoiceCode');

		$grid->addFilterDateRange('changeTime', 'messages.proformaInvoiceList.column.changeTime');
		$grid->addFilterDateRange('dueDate', 'messages.proformaInvoiceList.column.dueDate');
		$grid->addFilterDateRange('taxDate', 'messages.proformaInvoiceList.column.taxDate');

		$grid->cantSetHiddenColumn('isValid');
		$grid->cantSetHiddenColumn('code');
		$grid->setOuterFilterColumnsCount(3);

		return $grid;
	}

	public function handleSynchronize(int $id): void
	{
		/** @var ProformaInvoice $entity */
		$entity = $this->invoiceManager->find($this->securityUser->getProjectEntity(), $id);
		try {
			$entity = $this->invoiceManager->synchronizeFromShoptet($this->securityUser->getProjectEntity(), $entity->getShoptetCode());
			$this->getPresenter()->redrawControl('orderDetail');
			$this->getPresenter()->flashSuccess($this->translator->translate('messages.proformaInvoiceList.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->getPresenter()->flashError($this->translator->translate('messages.proformaInvoiceList.message.synchronize.error', ['code' => $entity->getCode()]));
		}

		$this->redirect('this');
	}

	public function render(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/default.latte');

		$this->getTemplate()->render();
	}
}
