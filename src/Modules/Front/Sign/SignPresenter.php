<?php

declare(strict_types=1);

namespace App\Modules\Front\Sign;

use App\Application;
use App\Exception\Logic\DuplicityException;
use App\Exception\Runtime\AuthenticationException;
use App\Facade\UserRegistrationFacade;
use App\Modules\Front\BaseFrontPresenter;
use App\Security\EmailVerifier;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Application\Attributes\Persistent;
use Nette\DI\Attributes\Inject;
use Nette\Utils\ArrayHash;

/**
 * @method SecurityUser getUser()
 */
final class SignPresenter extends BaseFrontPresenter
{
	#[Persistent]
	public ?string $backlink = null;

	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public EmailVerifier $emailVerifier;

	#[Inject]
	public UserRegistrationFacade $userRegistrationFacade;

	public function checkRequirements(mixed $element): void
	{
	}

	public function actionIn(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
		}
	}

	public function actionActivation(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
		}
		$this->emailVerifier->handleEmailConfirmation(
			request: $this->getHttpRequest(),
			user: $this->getUser()->getIdentity()->getEntity()
		);
		$this->flashSuccess('Your account was activated');
		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}

	public function actionOut(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->getUser()->logout(true);
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

	protected function createComponentRegistrationForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addValidationEmail('email', 'Email', true, true);
		$form->addPasswords('password', 'Password', 'Re-type password')
			->setRequired(true);
		$form->addText('firstName', 'First name');
		$form->addText('lastName', 'Last name');
		$form->addCheckbox('agreeTerms', 'Agree terms')
			->setRequired(true);
		$form->addSubmit('submit');
		$form->onSuccess[] = [$this, 'processRegistrationForm'];

		return $form;
	}


	public function processRegistrationForm(Form $form, ArrayHash $values): void
	{
		try {
			$userEntity = $this->userRegistrationFacade->createUser(
				$values->firstName,
				$values->lastName,
				$values->email,
				$values->password
			);
			$this->emailVerifier->sendEmailConfirmation(user: $userEntity);
			$this->getUser()->login($userEntity->toIdentity([]));
			$this->getUser()->logout();
		} catch (DuplicityException $e) {
			$form->addError('User with this email is already exists');

			return;
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}


	public function processLoginForm(Form $form, ArrayHash $values): void
	{
		try {
			$this->getUser()->setExpiration($values->remember ? '14 days' : '20 minutes');
			$this->getUser()->login($values->email, $values->password);
		} catch (AuthenticationException $e) {
			$form->addError('Invalid username or password');

			return;
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}
}
