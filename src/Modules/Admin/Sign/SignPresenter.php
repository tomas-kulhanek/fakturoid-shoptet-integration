<?php

declare(strict_types=1);

namespace App\Modules\Admin\Sign;

use App\Application;
use App\Exception\Runtime\AuthenticationException;
use App\Modules\Admin\BaseAdminPresenter;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Application\Attributes\Persistent;
use Nette\DI\Attributes\Inject;

final class SignPresenter extends BaseAdminPresenter
{
	#[Persistent]
	public ?string $backlink = null;

	#[Inject]
	public FormFactory $formFactory;

	public function checkRequirements(mixed $element): void
	{
	}

	public function actionIn(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
		}
	}

	public function actionOut(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->getUser()->logout();
			$this->flashSuccess('_front.sign.out.success');
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_OUT);
	}

	protected function createComponentLoginForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addValidationEmail('email');
		$form->addPassword('password')
			->setRequired(true);
		$form->addCheckbox('remember')
			->setDefaultValue(true);
		$form->addSubmit('submit');
		$form->onSuccess[] = [$this, 'processLoginForm'];

		return $form;
	}

	public function processLoginForm(Form $form): void
	{
		try {
			$this->getUser()->setExpiration($form->values->remember ? '14 days' : '20 minutes');
			$this->getUser()->login($form->values->email, $form->values->password);
		} catch (AuthenticationException $e) {
			$form->addError('Invalid username or password');

			return;
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}
}
