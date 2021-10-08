<?php

declare(strict_types=1);


namespace App\Wizard;

use App\Api\FakturoidFactory;
use App\Database\Entity\ProjectSetting;
use App\Modules\Base\BasePresenter;
use Contributte\FormWizard\Wizard;
use Nette\Application\UI\Form;
use Nette\Http\Session;

/**
 * @method \App\UI\Form createForm()
 * @method BasePresenter getPresenter()
 */
class InstallWizard extends Wizard
{
	/** @var array|string[] */
	private array $stepNames = [
		1 => "messages.installWizard.step.accounting",
		2 => "messages.installWizard.step.accountingConfirm",
		3 => "messages.installWizard.step.shoptet",
		4 => "messages.installWizard.step.mainSettings",
	];
	public function __construct(
		Session                  $session,
		private FakturoidFactory $fakturoidFactory
	) {
		parent::__construct($session);
	}

	protected function startup(): void
	{
		$wizard = $this;
		$this->setDefaultValues(2, function (\App\UI\Form $form, array $values) use ($wizard): void {
			try {
				$fakturoid = $wizard->fakturoidFactory->createClient(
					$values[1]['accountingAccount'],
					$values[1]['accountingEmail'],
					$values[1]['accountingApiKey']
				);
				$accountingData = $fakturoid->getAccount()->getBody();
				bdump($accountingData);
				$form->setDefaults(
					[
						'accountingPlan' => $accountingData->plan,
						'accountingName' => $accountingData->name,
						'accountingRegistrationNo' => $accountingData->registration_no,
						'accountingVatNo' => $accountingData->vat_no,
						'accountingStreet' => $accountingData->street,
						'accountingCity' => $accountingData->city,
						'accountingZip' => $accountingData->zip,
					]
				);
			} catch (\Throwable $exception) {
				bdump($exception);
				$form->addError('CHYBA!');
				$form->removeComponent($form->getComponent(self::NEXT_SUBMIT_NAME));
				$form->setDefaults(
					[
						'accountingPlan' => '',
						'accountingName' => '',
						'accountingRegistrationNo' => '',
						'accountingVatNo' => '',
						'accountingStreet' => '',
						'accountingCity' => '',
						'accountingZip' => '',
					]
				);
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
		$form->addSelect('automatization', 'messages.installWizard.field.one.automatization', [
			ProjectSetting::AUTOMATIZATION_MANUAL => 'messages.automatization.manual',
			ProjectSetting::AUTOMATIZATION_SEMI_AUTO => 'messages.automatization.semi',
			ProjectSetting::AUTOMATIZATION_AUTO => 'messages.automatization.auto',
		]);
		$form->addSubmit(self::PREV_SUBMIT_NAME, 'messages.installWizard.button.back');
		$form->addSubmit(self::FINISH_SUBMIT_NAME, 'messages.installWizard.button.complete')
			->getControlPrototype()->class('btn btn-success button');
		return $form;
	}
}
