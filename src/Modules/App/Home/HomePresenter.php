<?php

declare(strict_types=1);

namespace App\Modules\App\Home;

use App\Modules\App\BaseAppPresenter;
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

	public function handleChangeStep(int $step): void
	{
		$this['installWizard']->setStep($step);

		$this->redirect('wizard'); // Optional, hides parameter from URL
	}

	protected function createComponentInstallWizard(): InstallWizard
	{
		return $this->installWizard;
	}
}
