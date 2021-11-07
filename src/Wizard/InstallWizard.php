<?php

declare(strict_types=1);


namespace App\Wizard;

use App\Api\FakturoidFactory;
use App\Database\Entity\ProjectSetting;
use App\Modules\Base\BasePresenter;
use App\Security\SecurityUser;
use Contributte\FormWizard\Wizard;
use Nette\Application\UI\Form;
use Nette\Http\Session;
use Nette\Localization\Translator;

/**
 * @method \App\UI\Form createForm()
 * @method BasePresenter getPresenter()
 */
class InstallWizard extends Wizard
{
	/** @var array|string[] */
	private array $stepNames = [
		1 => "messages.installWizard.step.connecting",
		2 => "messages.installWizard.step.confirmation",
		3 => "messages.installWizard.step.accounting",
	];

	public function __construct(
		Session $session,
		private FakturoidFactory $fakturoidFactory,
		private Translator $translator,
		private SecurityUser $user
	) {
		parent::__construct($session);
	}

	protected function startup(): void
	{
		$wizard = $this;
		$this->setDefaultValues(2, function (\App\UI\Form $form, array $values) use ($wizard): void {
			bdump($wizard->getSection()->getValues());
			try {
				$wizard->getSection()->setStepValues(2, [
					'accountingPlan' => '',
					'accountingName' => '',
					'accountingRegistrationNo' => '',
					'accountingVatNo' => '',
					'accountingStreet' => '',
					'accountingCity' => '',
					'accountingZip' => '',
				]);
				$fakturoid = $wizard->fakturoidFactory->createClient(
					$values[1]['accountingAccount'],
					$values[1]['accountingEmail'],
					$values[1]['accountingApiKey']
				);
				$accountingData = $fakturoid->getAccount()->getBody();
				bdump($accountingData);
				$wizard->getSection()->setStepValues(2, [
					'accountingPlan' => $accountingData->plan,
					'accountingName' => $accountingData->name,
					'accountingRegistrationNo' => $accountingData->registration_no,
					'accountingVatNo' => $accountingData->vat_no,
					'accountingStreet' => $accountingData->street,
					'accountingCity' => $accountingData->city,
					'accountingZip' => $accountingData->zip,
				]);
				bdump($wizard->getSection()->getValues());
			} catch (\Throwable $exception) {
				bdump($exception);
				$form->removeComponent($form->getComponent(self::NEXT_SUBMIT_NAME));
			}
		});
		$this->setDefaultValues(3, function (\App\UI\Form $form, array $values): void {
			bdump($values);
		});
	}

	protected function finish(): void
	{
		$values = $this->getValues();
		bdump($values);
	}

	public function getStepData(int $step): array
	{
		return [
			'name' => $this->stepNames[$step],
		];
	}

	protected function createStep1(): Form
	{
		$form = $this->createForm();

		$form->addEmail('accountingEmail', 'messages.installWizard.field.one.accountingEmail')
			->setDefaultValue($this->user->getUserEntity()->getEmail())
			->setRequired();
		$form->addText('accountingAccount', 'messages.installWizard.field.one.accountingAccount')
			->setRequired();
		$form->addPassword('accountingApiKey', 'messages.installWizard.field.one.accountingApiKey')
			->setRequired();

		$form->addSubmit(self::NEXT_SUBMIT_NAME, 'messages.installWizard.button.next')
			->getControlPrototype()->class('btn btn-success button');

		return $form;
	}

	protected function createStep2(): Form
	{
		$form = $this->createForm();

		$form->addText('accountingPlan', 'messages.installWizard.field.two.plan')
			->getControlPrototype()->addAttributes(['readonly' => 'readonly']);
		$form->addText('accountingName', 'messages.installWizard.field.two.name')
			->getControlPrototype()->addAttributes(['readonly' => 'readonly']);
		$form->addText('accountingRegistrationNo', 'messages.installWizard.field.two.registrationNo')
			->getControlPrototype()->addAttributes(['readonly' => 'readonly']);
		$form->addText('accountingVatNo', 'messages.installWizard.field.two.vatNo')
			->getControlPrototype()->addAttributes(['readonly' => 'readonly']);
		$form->addText('accountingStreet', 'messages.installWizard.field.two.street')
			->getControlPrototype()->addAttributes(['readonly' => 'readonly']);
		$form->addText('accountingCity', 'messages.installWizard.field.two.city')
			->getControlPrototype()->addAttributes(['readonly' => 'readonly']);
		$form->addText('accountingZip', 'messages.installWizard.field.two.zip')
			->getControlPrototype()->addAttributes(['readonly' => 'readonly']);

		$form->addSubmit(self::PREV_SUBMIT_NAME, 'messages.installWizard.button.back');
		$form->addSubmit(self::NEXT_SUBMIT_NAME, 'messages.installWizard.button.two.next')
			->getControlPrototype()->class('btn btn-success button');

		return $form;
	}

	protected function createStep3(): Form
	{
		$form = $this->createForm();
		$form->addRadioList(
			name: 'automatization',
			label: '',
			items: [
				ProjectSetting::AUTOMATIZATION_MANUAL => 'messages.home.accounting.steps.three.automatizationInformation.li.one',
				ProjectSetting::AUTOMATIZATION_AUTO => 'messages.home.accounting.steps.three.automatizationInformation.li.three',
			]
		);
		$form->addText('customerName', 'messages.home.accounting.steps.three.endUser')
			->setRequired(true)
			->setDefaultValue(
				$this->translator->translate('messages.home.accounting.steps.three.endUser')
			);
		//$form->addCheckboxList(
		//	name: 'synchronize',
		//	label: 'messages.installWizard.field.three.synchronizeInformation',
		//	items: [
		//		'invoices' => 'messages.installWizard.field.three.synchronizeInvoices',
		//		'proformaInvoices' => 'messages.installWizard.field.three.synchronizeProformaInvoices',
		//	]
		//);


		$form->addSubmit(self::PREV_SUBMIT_NAME, 'messages.installWizard.button.back');
		$form->addSubmit(self::FINISH_SUBMIT_NAME, 'messages.installWizard.button.complete')
			->getControlPrototype()->class('btn btn-success button');

		return $form;
	}
}
