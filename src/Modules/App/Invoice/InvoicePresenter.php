<?php

declare(strict_types=1);


namespace App\Modules\App\Invoice;

use App\Application;
use App\Database\Entity\Shoptet\Invoice;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Facade\Fakturoid;
use App\Latte\NumberFormatter;
use App\Manager\InvoiceManager;
use App\Modules\App\BaseAppPresenter;
use App\Modules\App\Invoice\Component\Grid\InvoiceGrid;
use App\Modules\App\Invoice\Component\Grid\InvoiceGridFactory;
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
class InvoicePresenter extends BaseAppPresenter
{
	#[Inject]
	public NumberFormatter $numberFormatter;

	private ?Invoice $invoice = null;

	public function __construct(
		private Fakturoid\Invoice       $createInvoiceAccounting,
		private InvoiceManager          $invoiceManager,
		private FormFactory             $formFactory,
		private InvoiceGridFactory      $invoiceGridFactory
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

	protected function createComponentPageGrid(): InvoiceGrid
	{
		$dataSource = $this->invoiceManager->getRepository()->createQueryBuilder('i')
			->addSelect('id')
			->addSelect('ib')
			->leftJoin('i.deliveryAddress', 'id')
			->leftJoin('i.billingAddress', 'ib')
			->where('i.project = :project')
			->setParameter('project', $this->getUser()->getProjectEntity());

		return $this->invoiceGridFactory->create($dataSource);
	}

	protected function createComponentErrorPageGrid(): InvoiceGrid
	{
		$dataSource = $this->invoiceManager->getRepository()->createQueryBuilder('i')
			->addSelect('id')
			->addSelect('ib')
			->leftJoin('i.deliveryAddress', 'id')
			->leftJoin('i.billingAddress', 'ib')
			->where('i.project = :project')
			->andWhere('i.accountingError = 1')
			->setParameter('project', $this->getUser()->getProjectEntity());

		return $this->invoiceGridFactory->create($dataSource);
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
