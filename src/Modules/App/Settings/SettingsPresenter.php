<?php

declare(strict_types=1);

namespace App\Modules\App\Settings;

use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\ProjectSetting;
use App\Database\EntityManager;
use App\Manager\EshopInfoManager;
use App\Manager\OrderStatusManager;
use App\Manager\ProjectSettingsManager;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Tracy\Debugger;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
final class SettingsPresenter extends BaseAppPresenter
{
	#[Inject]
	public FormFactory $formFactory;
	#[Inject]
	public Translator $translator;

	#[Inject]
	public EshopInfoManager $eshopInfoManager;

	#[Inject]
	public ProjectSettingsManager $projectSettingsManager;

	#[Inject]
	public DataGridFactory $dataGridFactory;

	#[Inject]
	public EntityManager $entityManager;

	#[Inject]
	public OrderStatusManager $orderStatusManager;


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

	protected function createComponentBasicSettingForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addSelect('automatization', 'messages.app.settings.field.automatization', [
			ProjectSetting::AUTOMATIZATION_MANUAL => 'messages.automatization.manual',
			ProjectSetting::AUTOMATIZATION_SEMI_AUTO => 'messages.automatization.semi',
			ProjectSetting::AUTOMATIZATION_AUTO => 'messages.automatization.auto',
		]);
		$projectSetting = $this->getUser()->getProjectEntity()->getSettings();
		$form->addText('accountingApiKey', 'messages.app.settings.field.accountingApiKey')
			->setRequired($projectSetting->getAccountingApiKey() === null);

		$form->addEmail('accountingEmail', 'messages.app.settings.field.accountingEmail')
			->setRequired()
			->setDefaultValue($projectSetting->getAccountingEmail());

		$form->addText('accountingAccount', 'messages.app.settings.field.accountingAccount')
			->setRequired()
			->setDefaultValue($projectSetting->getAccountingAccount());

		$form->addCheckbox('clearApiKey', 'messages.app.settings.field.clearApiKey')
			->setDefaultValue(false);
		$form->addCheckbox('updateOrderStatuses', 'messages.app.settings.field.updateOrderStatuses')
			->setDefaultValue(false);
		$form->addCheckbox('propagateDeliveryAddress', 'messages.app.settings.field.propagateDeliveryAddress')
			->setDefaultValue($projectSetting->isPropagateDeliveryAddress());


		$form->addSubmit('submit');
		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			$this->projectSettingsManager->saveSettings(
				$this->getUser()->getProjectEntity(),
				$values->automatization,
				$values->accountingEmail,
				$values->accountingAccount,
				$values->propagateDeliveryAddress,
				$values->accountingApiKey,
				$values->clearApiKey
			);
			$this->flashSuccess(
				$this->translator->translate('messages.app.settings.successSaved')
			);
			if ($values->clearApiKey) {
				$this->flashWarning(
					$this->translator->translate('messages.app.settings.apiKeyCleared')
				);
			}
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
				$this->translator->translate('messages.app.settings.orderStatusSyncFail')
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
		$grid->addColumnStatus('createInvoice', 'messages.app.orderStatuses.createInvoice')
			->setCaret(false)
			->addOption(true, 'messages.app.orderStatuses.yes')
			->setClass('btn-success')
			->endOption()
			->addOption(false, 'messages.app.orderStatuses.no')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function (string $id, string $newStatus): void {
				$this->orderStatusManager->changeOption(
					optionName: 'createInvoice',
					ids: [$id],
					project: $this->getUser()->getProjectEntity(),
					newValue: $newStatus
				);
				if ($this->isAjax()) {
					$this['orderStatusGrid']->redrawItem($id);
				}
			};
		$grid->addColumnStatus('createProforma', 'messages.app.orderStatuses.createProforma')
			->setCaret(false)
			->addOption(true, 'messages.app.orderStatuses.yes')
			->setClass('btn-success')
			->endOption()
			->addOption(false, 'messages.app.orderStatuses.no')
			->setClass('btn-danger')
			->endOption()
			->onChange[] = function (string $id, string $newStatus): void {
				$this->orderStatusManager->changeOption(
					optionName: 'createProforma',
					ids: [$id],
					project: $this->getUser()->getProjectEntity(),
					newValue: $newStatus
				);
				if ($this->isAjax()) {
					$this['orderStatusGrid']->redrawItem($id);
				}
			};
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

		$grid->addGroupAction(
			'messages.app.orderStatuses.createInvoice',
			[
				1 => 'messages.app.orderStatuses.yes',
				0 => 'messages.app.orderStatuses.no',
			]
		)->onSelect[] = function (array $ids, $status): void {
			$this->orderStatusManager->changeOption(
				optionName: 'createInvoice',
				ids: $ids,
				project: $this->getUser()->getProjectEntity(),
				newValue: $status
			);
			if ($this->isAjax()) {
				$this->redrawControl('flashes');
				$this->redrawControl();
				$this['orderStatusGrid']->redrawControl();
			}
		};
		$grid->addGroupAction(
			'messages.app.orderStatuses.createProforma',
			[
				1 => 'messages.app.orderStatuses.yes',
				0 => 'messages.app.orderStatuses.no',
			]
		)->onSelect[] = function (array $ids, $status): void {
			$this->orderStatusManager->changeOption(
				optionName: 'createProforma',
				ids: $ids,
				project: $this->getUser()->getProjectEntity(),
				newValue: $status
			);
			if ($this->isAjax()) {
				$this->redrawControl('flashes');
				$this->redrawControl();
				$this['orderStatusGrid']->redrawControl();
			}
		};

		return $grid;
	}
}
