<?php

declare(strict_types=1);


namespace App\Modules\App\ProformaInvoice;

use App\Application;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Exception\Accounting\EmptyLines;
use App\Facade\Fakturoid;
use App\Latte\NumberFormatter;
use App\Manager\ProformaInvoiceManager;
use App\MessageBus\SynchronizeMessageBusDispatcher;
use App\Modules\App\BaseAppPresenter;
use App\Modules\App\ProformaInvoice\Component\Grid\ProformaInvoiceGrid;
use App\Modules\App\ProformaInvoice\Component\Grid\ProformaInvoiceGridFactory;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

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
		protected ProformaInvoiceManager          $invoiceManager,
		protected Fakturoid\ProformaInvoice       $createProformaInvoice,
		protected SynchronizeMessageBusDispatcher $synchronizeMessageBusDispatcher,
		private FormFactory                       $formFactory,
		private ProformaInvoiceGridFactory        $proformaInvoiceGridFactory
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

		$this->redirect('this');
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('pageDetail');
		}
		$this->proformaInvoice = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);

		bdump($this->proformaInvoice);
		$this->getTemplate()->setParameters([
			'invoice' => $this->proformaInvoice,
		]);
	}

	protected function createComponentPageGrid(): ProformaInvoiceGrid
	{
		return $this->proformaInvoiceGridFactory->create(
			$this->invoiceManager->getRepository()->createQueryBuilder('i')
				->addSelect('id')
				->addSelect('ib')
				->leftJoin('i.deliveryAddress', 'id')
				->leftJoin('i.billingAddress', 'ib')
				->where('i.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);
	}

	protected function createComponentInvoiceForm(): Form
	{
		$form = $this->formFactory->create();

		//$form->addSubmit('createInvoice', '')
		//	->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('createAccounting', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('updateAccounting', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('synchronize', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		/*
		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button * /
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
		*/
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
