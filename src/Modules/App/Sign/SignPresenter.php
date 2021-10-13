<?php

declare(strict_types=1);

namespace App\Modules\App\Sign;

use App\Application;
use App\Exception\Runtime\AuthenticationException;
use App\Manager\Core\ProjectManager;
use App\Modules\Front\BaseFrontPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\DI\Attributes\Inject;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;

/**
 * @method SecurityUser getUser()
 */
final class SignPresenter extends BaseFrontPresenter
{
	#[Inject]
	public Translator $translator;
	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public ProjectManager $projectManager;

	protected function startup()
	{
		parent::startup();
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(':App:Home:');
		}
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

	public function processLoginForm(Form $form, ArrayHash $values): void
	{
		try {
			//$this->getUser()->setExpiration($values->remember ? '14 days' : '20 minutes');
			$this->getUser()->login($values->email, $values->password);
		} catch (AuthenticationException) {
			$form->addError('Invalid username or password');

			return;
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}
}
