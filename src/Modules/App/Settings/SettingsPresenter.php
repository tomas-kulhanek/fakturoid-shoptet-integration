<?php

declare(strict_types=1);

namespace App\Modules\App\Settings;

use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Currency;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Manager\EshopInfoManager;
use App\Manager\OrderStatusManager;
use App\Manager\ProjectSettingsManager;
use App\Manager\WebhookManager;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Tracy\Debugger;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
final class SettingsPresenter extends BaseAppPresenter
{
	public function __construct(
		private FormFactory            $formFactory,
		private WebhookManager         $webhookManager,
		private EshopInfoManager       $eshopInfoManager,
		private ProjectSettingsManager $projectSettingsManager,
		private DataGridFactory        $dataGridFactory,
		private EntityManager          $entityManager,
		private OrderStatusManager     $orderStatusManager
	) {
		parent::__construct();
	}


	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('App:Settings')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
		if (!$this->getUser()->getProjectEntity()->isActive()) {
			$this->flashError('You cannot access this without active project');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	protected function beforeRender()
	{
		parent::beforeRender();

		$this->getTemplate()->setParameters(
			[
				'accountingNumberLineIdDescription' => $this->getTranslator()->translate(
					'messages.settings.accounting.accountingNumberLineIdDescription',
					[
						'accountingAccount' => $this->getUser()->getProjectEntity()->getSettings()->getAccountingAccount(),
					]
				),
			]
		);
	}

	public function actionAccounting(): void
	{
		$this->getTemplate()->setFile(__DIR__ . '/templates/default.latte');
	}

	protected function createComponentAccountingSettingForm(): Form
	{
		$form = $this->formFactory->create();
		$projectSetting = $this->getUser()->getProjectEntity()->getSettings();
		$form->addEmail('accountingEmail', 'messages.settings.accounting.accountingEmail')
			->setRequired();
		$form->addText('accountingAccount', 'messages.settings.accounting.accountingAccount')
			->setRequired();
		$form->addNumeric('accountingNumberLineId', 'messages.settings.accounting.accountingNumberLineId');
		$password = $form->addPassword('accountingApiKey', 'messages.settings.accounting.accountingApiKey');
		$password->setRequired(false)
			->getRules()->removeRule(Form::LENGTH);
		$password->getControlPrototype()->placeholder('messages.settings.accounting.accountingApiKeyPlaceholder');

		$form->addCheckbox('clearApiKey', 'messages.settings.accounting.clearApiKey')
			->setDefaultValue(false);
		$form->addCheckbox('propagateDeliveryAddress', 'messages.settings.accounting.propagateDeliveryAddress');
		$form->addCheckbox('accountingReminder', 'messages.settings.accounting.reminder');

		$form->setDefaults([
			'accountingEmail' => $projectSetting->getAccountingEmail(),
			'accountingAccount' => $projectSetting->getAccountingAccount(),
			'propagateDeliveryAddress' => $projectSetting->isPropagateDeliveryAddress(),
			'accountingReminder' => $projectSetting->isAccountingReminder(),
			'accountingNumberLineId' => $projectSetting->getAccountingNumberLineId()
		]);

		$form->addSubmit('submit', 'messages.settings.accounting.submit');

		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			$this->projectSettingsManager->saveAccountingSettings(
				$this->getUser()->getProjectEntity(),
				$values->accountingEmail,
				$values->accountingAccount,
				$values->accountingReminder,
				$values->propagateDeliveryAddress,
				$values->accountingApiKey,
				$values->clearApiKey,
				$values->accountingNumberLineId
			);
			$this->flashSuccess(
				$this->getTranslator()->translate('messages.settings.accounting.saved')
			);
			$this->redirect('this');
		};

		return $form;
	}

	protected function createComponentShoptetSettingForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addRadioList(
			name: 'automatization',
			label: '',
			items: [
				ProjectSetting::AUTOMATIZATION_MANUAL => 'messages.settings.shoptet.automatizationInformation.li.one',
				ProjectSetting::AUTOMATIZATION_AUTO => 'messages.settings.shoptet.automatizationInformation.li.three',
			]
		);
		$form->addCheckboxList(
			name: 'synchronize',
			label: 'messages.settings.shoptet.synchronizeInformation',
			items: [
				'invoices' => 'messages.settings.shoptet.synchronizeInvoices',
				'proformaInvoices' => 'messages.settings.shoptet.synchronizeProformaInvoices',
			]
		);
		$defaults = [
			'automatization' => $this->getUser()->getProjectEntity()->getSettings()->getAutomatization(),
		];
		if ($this->getUser()->getProjectEntity()->getSettings()->isShoptetSynchronizeInvoices()) {
			$defaults['synchronize'][] = 'invoices';
		}
		if ($this->getUser()->getProjectEntity()->getSettings()->isShoptetSynchronizeProformaInvoices()) {
			$defaults['synchronize'][] = 'proformaInvoices';
		}
		$form->setDefaults($defaults);

		$form->addSubmit('submit');

		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			$this->projectSettingsManager->saveShoptetSettings(
				$this->getUser()->getProjectEntity(),
				$values->automatization,
				(array) $values->synchronize
			);
			$this->flashSuccess(
				$this->getTranslator()->translate('messages.settings.shoptet.saved')
			);
			$this->redirect('this');
		};

		return $form;
	}

	public function handleSyncStatuses(): void
	{
		try {
			$this->eshopInfoManager->syncOrderStatuses(
				$this->getUser()->getProjectEntity()
			);
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashWarning(
				$this->getTranslator()->translate('messages.app.settings.orderStatusSyncFail')
			);
		}

		if ($this->isAjax()) {
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}

	protected function createComponentOrderStatusGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create(false, false);
		$grid->setDataSource(
			$this->entityManager->getRepository(OrderStatus::class)
				->createQueryBuilder('o')
				->where('o.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);

		$grid->addToolbarButton('syncStatuses!', 'messages.app.orderStatuses.synchronize');
		$grid->addColumnText('name', 'messages.app.orderStatuses.name');

		$grid->addColumnText('markAsPaid', 'messages.app.orderStatuses.markAsPaid')
			->setRenderer(function (OrderStatus $invoice): Html {
				if ($invoice->isMarkAsPaid()) {
					return
						Html::el('i')
							->class('fa fa-check-circle text-success');
				}
				return
					Html::el('i')
						->class('text-danger fa fa-times-circle');
			});
		//$grid->addColumnStatus('createInvoice', 'messages.app.orderStatuses.createInvoice')
		//	->setCaret(false)
		//	->addOption(true, 'messages.app.orderStatuses.yes')
		//	->setClass('btn-success')
		//	->endOption()
		//	->addOption(false, 'messages.app.orderStatuses.no')
		//	->setClass('btn-danger')
		//	->endOption()
		//	->onChange[] = function (string $id, string $newStatus): void {
		//		$this->orderStatusManager->changeOption(
		//			optionName: 'createInvoice',
		//			ids: [$id],
		//			project: $this->getUser()->getProjectEntity(),
		//			newValue: $newStatus
		//		);
		//		if ($this->isAjax()) {
		//			$this['orderStatusGrid']->redrawItem($id);
		//		}
		//	};
		//$grid->addColumnStatus('createProforma', 'messages.app.orderStatuses.createProforma')
		//	->setCaret(false)
		//	->addOption(true, 'messages.app.orderStatuses.yes')
		//	->setClass('btn-success')
		//	->endOption()
		//	->addOption(false, 'messages.app.orderStatuses.no')
		//	->setClass('btn-danger')
		//	->endOption()
		//	->onChange[] = function (string $id, string $newStatus): void {
		//		$this->orderStatusManager->changeOption(
		//			optionName: 'createProforma',
		//			ids: [$id],
		//			project: $this->getUser()->getProjectEntity(),
		//			newValue: $newStatus
		//		);
		//		if ($this->isAjax()) {
		//			$this['orderStatusGrid']->redrawItem($id);
		//		}
		//	};
		$grid->addColumnStatus('type', 'messages.app.orderStatuses.color')
			->setCaret(false)
			->addOption('primary', 'messages.app.orderStatuses.type.primary')
			->setClass('btn-primary')
			->endOption()
			->addOption('danger', 'messages.app.orderStatuses.type.danger')
			->setClass('btn-danger')
			->endOption()
			->addOption('success', 'messages.app.orderStatuses.type.success')
			->setClass('btn-success')
			->endOption()
			->addOption('warning', 'messages.app.orderStatuses.type.warning')
			->setClass('btn-warning')
			->endOption()
			->addOption('info', 'messages.app.orderStatuses.type.info')
			->setClass('btn-info')
			->endOption()
			->onChange[] = function (string $id, string $newStatus): void {
				$this->orderStatusManager->changeOption(
					optionName: 'type',
					ids: [$id],
					project: $this->getUser()->getProjectEntity(),
					newValue: $newStatus
				);
				if ($this->isAjax()) {
					$this['orderStatusGrid']->redrawItem($id);
				}
			};

		return $grid;
	}

	protected function createComponentCurrenciesGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create(false, false);
		$grid->setDataSource(
			$this->entityManager->getRepository(Currency::class)
				->createQueryBuilder('o')
				->addSelect('ob')
				->leftJoin('o.bankAccount', 'ob')
				->where('o.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);
		$bankAccounts = $this->entityManager->getRepository(BankAccount::class)
			->findBy(['project' => $this->getUser()->getProjectEntity()]);

		$grid->addColumnText('cashdesk', 'messages.app.currencies.cashdesk')
			->setRenderer(function (Currency $currency): Html {
				if ($currency->isCashdesk()) {
					return Html::el('span')
						->class('fas fa-cash-register')
						->addText(' Cashdesk');
				}
				return Html::el('span')
					->class('fas fa-shopping-cart')
					->addText(' E-shop');
			});
		$grid->addColumnText('code', 'messages.app.currencies.code');
		$grid->addColumnText('title', 'messages.app.currencies.title');

		$options = [];
		/** @var BankAccount $bankAccount */
		foreach ($bankAccounts as $bankAccount) {
			$options[$bankAccount->getId()] = $bankAccount->getName() . ($bankAccount->getNumber() !== '' && $bankAccount->getNumber() !== null ? ' (' . $bankAccount->getNumber() . ')' : null);
		}
		$presenter = $this;
		$grid->addColumnStatus('bankAccount', 'messages.app.currencies.accountingBank', 'bankAccount.id')
			->setOptions($options)
			->onChange[] = function (string $id, string $newValue) use ($presenter): void {
				$entity = $presenter->entityManager->getRepository(Currency::class)
				->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);
				$entityAccounting = $presenter->entityManager->getRepository(BankAccount::class)
				->findOneBy(['id' => $newValue, 'project' => $this->getUser()->getProjectEntity()]);
				if ($entity instanceof Currency) {
					if ($entityAccounting instanceof BankAccount) {
						$entity->setBankAccount($entityAccounting);
					} else {
						$entity->setBankAccount(null);
					}
					$presenter->entityManager->flush($entity);
				}
				$this['currenciesGrid']->redrawItem($id);
			};

		return $grid;
	}

	public function handleWebhookReInit(): void
	{
		if (!$this->getUser()->isInRole(User::ROLE_SUPERADMIN)) {
			$this->redirect('default');
		}
		$this->webhookManager->reInitWebhooks($this->getUser()->getProjectEntity());
		$this->redirect('default');
	}
}
