<?php

declare(strict_types=1);

namespace App\Modules\App\Home;

use App\Api\ClientInterface;
use App\Application;
use App\Database\EntityManager;
use App\Manager\ProjectManager;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecretVault\ISecretVault;
use App\Security\SecurityUser;
use App\Wizard\InstallWizard;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
final class HomePresenter extends BaseAppPresenter
{
	#[Inject]
	public InstallWizard $installWizard;

	#[Inject]
	public ISecretVault $secretVault;

	#[Inject]
	public EntityManager $entityManager;

	#[Inject]
	public ProjectManager $projectManager;

	public function actionDefault(): void
	{
		if ($this->getUser()->getProjectEntity()->isSuspended()) {
			$this->getUser()->logout(true);
			$this->redirect(Application::DESTINATION_SIGN_IN);
		}
		if (!$this->getUser()->getProjectEntity()->isActive()) {
			$this->redirect('settings');
		}
		$this->redirect(':App:Invoice:list');
	}

	public function actionSettings(): void
	{
		if ($this->getUser()->getProjectEntity()->isActive()) {
			$this->redirect('default');
		}
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

	public function handleChangeStep(int $step): void
	{
		$this['installWizard']->setStep($step);

		$this->redirect('this');
	}

	public function actionOut(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->getUser()->logout(true);
			$this->flashSuccess($this->translator->translate('messages.sign.out'));
		}
		$this->redirect(Application::DESTINATION_AFTER_SIGN_OUT);
	}

	protected function createComponentInstallWizard(): InstallWizard
	{
		$this->installWizard->onSuccess[] = function (InstallWizard $installWizard): void {
			$values = $installWizard->getValues();
			bdump($values);
			$this->projectManager->initializeProject(
				$this->getUser()->getProjectEntity(),
				$values->accountingAccount,
				$values->accountingEmail,
				$values->accountingApiKey,
				(int)$values->accountingNumberLineId,
				(array) $values->synchronize,
				$values->customerName,
				\DateTimeImmutable::createFromMutable($values->documentDownloadFrom),
				$values->enableAccountingUpdate,
				$values->automatization
			);
		};

		return $this->installWizard;
	}
}
