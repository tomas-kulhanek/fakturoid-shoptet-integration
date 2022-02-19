<?php

declare(strict_types=1);


namespace App\Modules\App\Invoice;

use App\Application;
use App\Database\Entity\Shoptet\Invoice;
use App\Manager\InvoiceManager;
use App\MessageBus\AccountingBusDispatcher;
use App\Modules\App\BaseAppPresenter;
use App\Modules\App\Invoice\Component\Grid\InvoiceGrid;
use App\Modules\App\Invoice\Component\Grid\InvoiceGridFactory;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Forms\Controls\SubmitButton;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class InvoicePresenter extends BaseAppPresenter
{
	private ?Invoice $invoice = null;

	public function __construct(
		private InvoiceManager          $invoiceManager,
		private FormFactory             $formFactory,
		private InvoiceGridFactory      $invoiceGridFactory,
		private AccountingBusDispatcher $accountingBusDispatcher
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed(\App\Security\Authorizator\StaticAuthorizator::RESOURCE_INVOICE)) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
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
			/** @var SubmitButton $createAccounting */
			$createAccounting = $form->getComponent('createAccounting');
			/** @var SubmitButton $updateAccounting */
			$updateAccounting = $form->getComponent('updateAccounting');
			if (!$createAccounting->isSubmittedBy() && !$updateAccounting->isSubmittedBy()) {
				return;
			}
			$this->accountingBusDispatcher->dispatch($this->invoice);
			$this->flashSuccess(
				$this->getTranslator()->translate('messages.invoiceDetail.message.createAccounting.success')
			);
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
