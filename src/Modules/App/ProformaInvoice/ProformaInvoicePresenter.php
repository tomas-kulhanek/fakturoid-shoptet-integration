<?php

declare(strict_types=1);


namespace App\Modules\App\ProformaInvoice;

use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Exception\Accounting\EmptyLines;
use App\Facade\Fakturoid;
use App\Facade\InvoiceCreateFacade;
use App\Latte\NumberFormatter;
use App\Manager\ProformaInvoiceManager;
use App\MessageBus\SynchronizeMessageBusDispatcher;
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
class ProformaInvoicePresenter extends BaseAppPresenter
{
	#[Inject]
	public NumberFormatter $numberFormatter;

	#[Inject]
	public Fakturoid\Invoice $invoiceFakturoid;

	private ?ProformaInvoice $proformaInvoice = null;

	public function __construct(
		private DataGridFactory                   $dataGridFactory,
		protected ProformaInvoiceManager          $invoiceManager,
		protected Fakturoid\ProformaInvoice       $createProformaInvoice,
		protected SynchronizeMessageBusDispatcher $synchronizeMessageBusDispatcher,
		private InvoiceCreateFacade               $invoiceCreateFacade,
		private FormFactory                       $formFactory
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('App:ProformaInvoice')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	public function handleSynchronize(int $id): void
	{
		/** @var ProformaInvoice $entity */
		$entity = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		try {
			$entity = $this->invoiceManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $entity->getShoptetCode());
			$this->redrawControl('orderDetail');
			$this->flashSuccess($this->getTranslator()->translate('messages.proformaInvoiceList.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->getTranslator()->translate('messages.proformaInvoiceList.message.synchronize.error', ['code' => $entity->getCode()]));
		}
		if ($this->isAjax()) {
			$this->redrawControl('flashes');
			$this['pageGrid']->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('pageDetail');
		}
		$this->proformaInvoice = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		;
		bdump($this->proformaInvoice);
		$this->getTemplate()->setParameters([
			'invoice' => $this->proformaInvoice,
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
			->setRenderer(function (ProformaInvoice $invoice): Html {
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
		$grid->addColumnDateTime('creationTime', 'messages.proformaInvoiceList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.proformaInvoiceList.column.changeTime')
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

		$grid->addColumnDateTime('deletedAt', 'messages.proformaInvoiceList.column.deletedAt')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addAction('detail', '', 'detail')
			->setRenderCondition(fn (Document $document) => !$document->isDeleted())
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addGroupAction(
			'messages.proformaInvoiceList.uploadToAccounting'
		)->onSelect[] = function (array $ids): void {
			$results = ['success' => [], 'invoiceExists' => [], 'error' => []];
			foreach ($ids as $id) {
				$invoice = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
				bdump($invoice);
				if ($invoice->getInvoice() instanceof \App\Database\Entity\Shoptet\Invoice) {
					$results['invoiceExists'][] = $invoice->getCode();

					continue;
				}
				try {
					if ($invoice->getAccountingId() === null) {
						$this->createProformaInvoice->create(invoice: $invoice);
					} else {
						$this->createProformaInvoice->update(invoice: $invoice);
					}
					$results['success'][] = $invoice->getCode();
				} catch (Exception $exception) {
					Debugger::log($exception);
					$results['error'][] = $invoice->getCode();
				}
			}
			if (count($results['invoiceExists']) > 0) {
				$this->flashWarning(
					$this->translator->translate(
						'messages.proformaInvoiceList.message.massUploadToAccounting.invoiceExists',
						[
							'codes' => implode(', ', $results['invoiceExists']),
						]
					)
				);
			}
			if (count($results['error']) > 0) {
				$this->flashError(
					$this->translator->translate(
						'messages.proformaInvoiceList.message.massUploadToAccounting.error',
						[
							'codes' => implode(', ', $results['error']),
						]
					)
				);
			}
			if (count($results['success']) > 0) {
				$this->flashSuccess(
					$this->translator->translate(
						'messages.proformaInvoiceList.message.massUploadToAccounting.success',
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

		$grid->addAction('accounting', '')
			->setRenderCondition(fn (ProformaInvoice $document) => $document->getAccountingPublicHtmlUrl() !== null && !$document->isDeleted())
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
					function (ProformaInvoice $item) use ($presenter): string {
						return $presenter->translator->translate('messages.proformaInvoiceList.synchronizeQuestion', ['code' => $item->getCode()]);
					}
				)
			);

		$grid->addFilterDateRange('creationTime', 'messages.proformaInvoiceList.column.creationTime');

		$grid->addFilterText('orderCode', 'messages.proformaInvoiceList.column.orderCode');
		$grid->addFilterText('proformaInvoiceCode', 'messages.proformaInvoiceList.column.proformaInvoiceCode');

		$grid->addFilterDateRange('changeTime', 'messages.proformaInvoiceList.column.changeTime');
		$grid->addFilterDateRange('dueDate', 'messages.proformaInvoiceList.column.dueDate');
		$grid->addFilterDateRange('taxDate', 'messages.proformaInvoiceList.column.taxDate');

		$grid->setRowCallback(function (Document $order, Html $tr): void {
			if ($order->getDeletedAt() instanceof \DateTimeImmutable) {
				$tr->addClass('bg-danger');
			}
		});
		$grid->allowRowsGroupAction(fn (Document $document) => !$document->isDeleted());

		$grid->cantSetHiddenColumn('isValid');
		$grid->cantSetHiddenColumn('code');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}

	protected function createComponentInvoiceForm(): Form
	{
		$form = $this->formFactory->create();


		$form->addSubmit('createInvoice', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('createAccounting', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('updateAccounting', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('synchronize', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('createInvoice');
			if (!$button->isSubmittedBy()) {
				return;
			}
			try {
				$invoice = $this->invoiceCreateFacade->createFromProforma($this->proformaInvoice);
				if ($this->proformaInvoice->getAccountingId() !== null) {
					$this->createProformaInvoice->markAsPaid($this->proformaInvoice, new \DateTimeImmutable());
					$this->invoiceFakturoid->refresh($invoice);
				}
				$this->flashSuccess(
					$this->getTranslator()->translate('messages.invoiceDetail.message.createInvoice.success', ['code' => $invoice->getCode()])
				);
			} catch (\Throwable $exception) {
				Debugger::log($exception);
				$this->flashError($this->getTranslator()->translate('messages.invoiceDetail.message.createInvoice.error'));
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
			$this->invoiceManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $this->proformaInvoice->getShoptetCode());
			try {
				$this->flashSuccess(
					$this->getTranslator()->translate('messages.invoiceDetail.message.synchronize.success', ['code' => $this->proformaInvoice->getCode()])
				);
			} catch (\Throwable $exception) {
				Debugger::log($exception);
				$this->flashError($this->getTranslator()->translate('messages.invoiceDetail.message.synchronize.error', ['code' => $this->proformaInvoice->getCode()]));
			}
			$this->redrawControl('orderDetail');
			$this->redirect('this');
		};

		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('createAccounting');
			if (!$button->isSubmittedBy()) {
				return;
			}
			if ($this->proformaInvoice->getAccountingSubjectId() === null) {
				try {
					$this->createProformaInvoice->create(invoice: $this->proformaInvoice);
					$this->flashSuccess(
						$this->getTranslator()->translate('messages.proformaInvoiceDetail.message.createAccounting.success')
					);
				} catch (EmptyLines) {
					$this->flashWarning(
						$this->getTranslator()->translate('messages.proformaInvoiceDetail.message.createAccounting.emptyLines')
					);
				}
			} else {
				$this->flashWarning(
					$this->getTranslator()->translate('messages.proformaInvoiceDetail.message.createAccounting.alreadyExists')
				);
			}
			$this->redirect('this');
		};
		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('updateAccounting');
			if (!$button->isSubmittedBy()) {
				return;
			}
			if ($this->proformaInvoice->getAccountingSubjectId() !== null) {
				try {
					$this->createProformaInvoice->update(invoice: $this->proformaInvoice);
					$this->flashSuccess(
						$this->getTranslator()->translate('messages.proformaInvoiceDetail.message.createAccounting.success')
					);
				} catch (EmptyLines) {
					$this->flashWarning(
						$this->getTranslator()->translate('messages.proformaInvoiceDetail.message.createAccounting.emptyLines')
					);
				}
			} else {
				$this->flashWarning(
					$this->getTranslator()->translate('messages.proformaInvoiceDetail.message.createAccounting.alreadyExists')
				);
			}
			$this->redirect('this');
		};

		return $form;
	}
}
