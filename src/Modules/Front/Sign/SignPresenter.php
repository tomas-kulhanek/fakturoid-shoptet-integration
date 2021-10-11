<?php

declare(strict_types=1);

namespace App\Modules\Front\Sign;

use App\Manager\Core\ProjectManager;
use App\Modules\Front\BaseFrontPresenter;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Database\Row;
use Nette\DI\Attributes\Inject;
use Nette\Http\Url;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;

final class SignPresenter extends BaseFrontPresenter
{
	#[Inject]
	public Translator $translator;
	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public ProjectManager $projectManager;

	protected function createComponentOauth(): Form
	{
		$form = $this->formFactory->create();

		$form->addText('shopUrl', 'eshop url')
			->setDefaultValue('shoptet.tomaskulhanek.cz');

		$form->addSubmit('submit');

		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			$url = new Url();
			$url->setScheme('https');
			$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $values->shopUrl));

			$project = $this->projectManager->getProjectByUrl($url);
			if (!$project instanceof Row) {
				$this->flashError($this->translator->translate('messages.sign.in.missingShop', ['shop' => $url->getAbsoluteUrl()]));
				$this->redirect('this');
			}

			$this->redirect(':App:Sign:in', ['projectId' => (int) $project->eshop_id]);
		};
		return $form;
	}
}
