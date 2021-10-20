<?php

declare(strict_types=1);

namespace App\Modules\Front\Sign;

use App\Application;
use App\Exception\Logic\NotFoundException;
use App\Exception\Runtime\AuthenticationException;
use App\Manager\UserManager;
use App\Modules\Front\BaseFrontPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Application\Attributes\Persistent;
use Nette\DI\Attributes\Inject;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;

/**
 * @method SecurityUser getUser()
 */
final class SignPresenter extends BaseFrontPresenter
{
	#[Persistent]
	public ?string $backlink = null;

	#[Inject]
	public Translator $translator;

	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public UserManager $userManager;


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
			$this->getUser()->logout(true);
			$this->flashSuccess($this->translator->translate('messages.sign.out'));
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

		$form->addText('shopUrl', 'eshop url');

		$form->onSuccess[] = [$this, 'processLoginForm'];

		return $form;
	}

	public function processLoginForm(Form $form, ArrayHash $values): void
	{
		try {
			$this->getUser()->setExpiration($values->remember ? '14 days' : '20 minutes');
			$this->userManager->authenticate(
				$values->shopUrl,
				$values->email,
				$values->password
			);
		} catch (AuthenticationException) {
			$this->flashError(
				$this->translator->translate('messages.sign.in.credentialsMissmatch')
			);
			return;
		} catch (NotFoundException) {
			$this->flashError(
				$this->translator->translate('messages.sign.in.missingShop', ['shop' => $values->shopUrl])
			);
			return;
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}
}
