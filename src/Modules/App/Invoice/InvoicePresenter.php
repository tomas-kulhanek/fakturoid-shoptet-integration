<?php

declare(strict_types=1);


namespace App\Modules\App\Invoice;

use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\Invoice;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Latte\NumberFormatter;
use App\Manager\InvoiceManager;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Fakturoid\Exception;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class InvoicePresenter extends BaseAppPresenter
{
	#[Inject]
	public NumberFormatter $numberFormatter;

	private ?Invoice $invoice = null;

	public function __construct(
		private DataGridFactory   $dataGridFactory,
		private Fakturoid\Invoice $createInvoiceAccounting,
		private InvoiceManager    $invoiceManager,
		private FormFactory       $formFactory
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('App:Invoice')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	public function handleSynchronize(int $id): void
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
		$this->invoice = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		bdump($this->invoice);
		$this->getTemplate()->setParameters([
			'invoice' => $this->invoice,
		]);
	}

	protected function createComponentPageGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'desc']);
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
				if ($invoice->isDeleted()) {
					return
						Html::el('i')
							->class('fa fa-trash');
				}
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

		$grid->addColumnDateTime('deletedAt', 'messages.invoiceList.column.deletedAt')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addGroupAction(
			'messages.invoiceList.uploadToAccounting'
		)->onSelect[] = function (array $ids): void {
			$results = ['success' => [], 'error' => []];
			foreach ($ids as $id) {
				$invoice = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
				bdump($invoice);
				try {
					if ($invoice->getAccountingId() === null) {
						$this->createInvoiceAccounting->create(invoice: $invoice);
					} else {
						$this->createInvoiceAccounting->update(invoice: $invoice);
					}
					$results['success'][] = $invoice->getCode();
				} catch (FakturoidException $exception) {
					Debugger::log($exception);
					$this->flashError($invoice->getCode() . ' - ' . $exception->humanize());
				}
			}
			if (count($results['success']) > 0) {
				$this->flashSuccess(
					$this->translator->translate(
						'messages.invoiceList.message.massUploadToAccounting.success',
						[
							'codes' => implode(', ', $results['success']),
						]
					)
				);
			}
			if ($this->isAjax()) {
				$this->redrawControl('flashes');
				$this->redrawControl();
				$this['pageGrid']->redrawControl();
			}
		};
		$presenter = $this;

		$grid->setRowCallback(function (Document $document, Html $tr): void {
			if ($document->isDeleted()) {
				$tr->addClass('bg-danger disabled');
			} elseif (!$document->isDeleted() && (!$document->getAccountingUpdatedAt() instanceof \DateTimeImmutable || $document->getAccountingUpdatedAt() < $document->getChangeTime())) {
				$tr->addClass('bg-danger');
			}
		});
		$grid->allowRowsGroupAction(fn (Document $document) => !$document->isDeleted());
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
		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setRenderCondition(fn (Document $document) => $document->getShoptetCode() !== null && $document->getShoptetCode() !== '' && !$document->isDeleted())
			->setConfirmation(
				new CallbackConfirmation(
					function (Invoice $item) use ($presenter): string {
						return $presenter->translator->translate('messages.invoiceList.synchronizeQuestion', ['code' => $item->getCode()]);
					}
				)
			);
		$grid->addAction('detail', '', 'detail')
			->setRenderCondition(fn (Document $document) => !$document->isDeleted())
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');


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

	protected function createComponentInvoiceForm(): Form
	{
		$form = $this->formFactory->create();

		$form->addSubmit('createAccounting', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('updateAccounting', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('synchronize', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('createAccounting');
			if (!$button->isSubmittedBy()) {
				return;
			}
			if ($this->invoice->getAccountingId() === null) {
				try {
					$this->createInvoiceAccounting->create(invoice: $this->invoice);
					$this->flashSuccess(
						$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.success')
					);
				} catch (EmptyLines) {
					$this->flashWarning(
						$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.emptyLines')
					);
				}
			} else {
				$this->flashWarning(
					$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.alreadyExists')
				);
			}
			$this->redrawControl('orderDetail');
			$this->redirect('this');
		};
		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('updateAccounting');
			if (!$button->isSubmittedBy()) {
				return;
			}
			if ($this->invoice->getAccountingId() !== null) {
				try {
					$this->createInvoiceAccounting->update(invoice: $this->invoice);
					$this->flashSuccess(
						$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.success')
					);
				} catch (EmptyLines) {
					$this->flashWarning(
						$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.emptyLines')
					);
				} catch (FakturoidException $exception) {
					$this->flashError($exception->humanize());
				}
			} else {
				$this->flashWarning(
					$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.alreadyExists')
				);
			}
			$this->redrawControl('orderDetail');
			$this->redirect('this');
		};
		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('synchronize');
			if (!$button->isSubmittedBy()) {
				return;
			}
			$this->invoiceManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $this->invoice->getShoptetCode());
			try {
				$this->flashSuccess(
					$this->getTranslator()->translate('messages.invoiceDetail.message.synchronize.success', ['code' => $this->invoice->getCode()])
				);
			} catch (\Throwable $exception) {
				Debugger::log($exception);
				$this->flashError($this->getTranslator()->translate('messages.invoiceDetail.message.synchronize.error', ['code' => $this->invoice->getCode()]));
			}
			$this->redrawControl('orderDetail');
			$this->redirect('this');
		};

		return $form;
	}
}
