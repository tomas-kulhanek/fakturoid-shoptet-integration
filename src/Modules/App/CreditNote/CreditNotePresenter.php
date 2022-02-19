<?php

declare(strict_types=1);

namespace App\Modules\App\CreditNote;

use App\Application;
use App\Database\Entity\Shoptet\CreditNote;
use App\Manager\CreditNoteManager;
use App\MessageBus\AccountingBusDispatcher;
use App\Modules\App\BaseAppPresenter;
use App\Modules\App\CreditNote\Component\Grid\CreditNoteGrid;
use App\Modules\App\CreditNote\Component\Grid\CreditNoteGridFactory;
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
class CreditNotePresenter extends BaseAppPresenter
{
	private ?CreditNote $creditNote = null;

	public function __construct(
		private CreditNoteManager       $creditNoteManager,
		private CreditNoteGridFactory   $creditNoteGridFactory,
		private FormFactory             $formFactory,
		private AccountingBusDispatcher $accountingBusDispatcher
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed(\App\Security\Authorizator\StaticAuthorizator::RESOURCE_CREDIT_NOTE)) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('pageDetail');
		}
		$this->creditNote = $this->creditNoteManager->find($this->getUser()->getProjectEntity(), $id);
		bdump($this->creditNote);
		$this->getTemplate()->setParameters([
			'creditNote' => $this->creditNote,
		]);
	}


	protected function createComponentPageGrid(): CreditNoteGrid
	{
		$dataSource = $this->creditNoteManager->getRepository()->createQueryBuilder('i')
			->addSelect('id')
			->addSelect('ib')
			->leftJoin('i.deliveryAddress', 'id')
			->leftJoin('i.billingAddress', 'ib')
			->where('i.project = :project')
			->setParameter('project', $this->getUser()->getProjectEntity());

		return $this->creditNoteGridFactory->create($dataSource);
	}

	protected function createComponentCreditNoteForm(): Form
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
			$this->accountingBusDispatcher->dispatch($this->creditNote);
			$this->flashSuccess(
				$this->getTranslator()->translate('messages.creditNoteDetail.message.createAccounting.success')
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
			$this->creditNoteManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $this->creditNote->getShoptetCode());
			try {
				$this->flashSuccess(
					$this->getTranslator()->translate('messages.creditNoteDetail.message.synchronize.success', ['code' => $this->creditNote->getCode()])
				);
			} catch (\Throwable $exception) {
				Debugger::log($exception);
				$this->flashError($this->getTranslator()->translate('messages.creditNoteDetail.message.synchronize.error', ['code' => $this->creditNote->getCode()]));
			}
			$this->redrawControl('orderDetail');
			$this->redirect('this');
		};

		return $form;
	}
}
