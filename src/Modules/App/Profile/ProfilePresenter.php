<?php

declare(strict_types=1);

namespace App\Modules\App\Profile;

use App\Application;
use App\Exception\Runtime\AuthenticationException;
use App\Manager\UserManager;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;
use Nette\Utils\ArrayHash;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
final class ProfilePresenter extends BaseAppPresenter
{
	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public UserManager $userManager;

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('App:Profile')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	protected function createComponentPasswordChange(): Form
	{
		$form = $this->formFactory->create();

		$form->addPasswords('password', 'messages.profile.changePassword.password', 'messages.profile.changePassword.passwordAgain');
		$form->addPassword('oldPassword', 'messages.profile.changePassword.oldPassword');
		$form->addSubmit('submit', 'messages.profile.changePassword.submit');

		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			try {
				$this->userManager->changePassword($this->getUser()->getUserEntity(), $values->oldPassword, $values->password);
				$this->getUser()->logout(true);
				$this->flashSuccess(
					$this->getTranslator()->translate('messages.profile.changePassword.passwordChanged')
				);
				$this->redirect(Application::DESTINATION_SIGN_IN);
			} catch (AuthenticationException) {
				$this->flashSuccess(
					$this->getTranslator()->translate('messages.profile.changePassword.oldPasswordIsNotCorrect')
				);
				return;
			}
		};
		return $form;
	}
}
