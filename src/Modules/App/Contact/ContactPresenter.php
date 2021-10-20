<?php

declare(strict_types=1);

namespace App\Modules\App\Contact;

use App\Manager\TicketManager;
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
final class ContactPresenter extends BaseAppPresenter
{
	#[Inject]
	public FormFactory $formFactory;
	#[Inject]
	public TicketManager $ticketManager;

	protected function createComponentContactForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addTextArea('message', 'messages.tickets.message', null, 20);

		$form->addSubmit('submit', 'messages.tickets.submit');
		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			$this->ticketManager->send($this->getUser()->getUserEntity(), $values->message);
			$this->flashSuccess(
				$this->getTranslator()->translate('messages.tickets.success')
			);
			$this->redirect('this');
		};

		return $form;
	}
}
