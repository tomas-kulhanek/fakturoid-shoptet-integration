<?php

declare(strict_types=1);

namespace App\Modules\App\Sign;

use App\Api\ClientInterface;
use App\Application;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\DBAL\MultiDbConnectionWrapper;
use App\DTO\Shoptet\AccessToken;
use App\Exception\Runtime\AuthenticationException;
use App\Facade\UserRegistrationFacade;
use App\Manager\Core\ProjectManager;
use App\Modules\Front\BaseFrontPresenter;
use App\Security\Identity;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\NoResultException;
use GuzzleHttp\Exception\ClientException;
use Nette\Application\Attributes\Persistent;
use Nette\Application\LinkGenerator;
use Nette\Database\Row;
use Nette\DI\Attributes\Inject;
use Nette\Http\Url;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Ramsey\Uuid\Uuid;
use Tracy\Debugger;

/**
 * @method SecurityUser getUser()
 * @property MultiDbConnectionWrapper $connectionWrapper
 */
final class SignPresenter extends BaseFrontPresenter
{
	#[Inject]
	public Translator $translator;
	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public ProjectManager $projectManager;

	#[Inject]
	public Connection $connection;

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
