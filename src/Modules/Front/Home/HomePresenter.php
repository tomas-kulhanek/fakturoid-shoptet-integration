<?php

declare(strict_types=1);

namespace App\Modules\Front\Home;

use App\Manager\TicketManager;
use App\Modules\Front\BaseFrontPresenter;
use App\UI\Form;
use Nette\DI\Attributes\Inject;
use Nette\Utils\ArrayHash;
use Tracy\Debugger;

final class HomePresenter extends BaseFrontPresenter
{
	private const MIN_DESCRIPTION_LENGTH = 50;

	#[Inject]
	public TicketManager $ticketManager;

	protected function createComponentMailForm(): Form
	{
		$form = $this->getFormFactory()->create(true);
		$form->addText('name', 'Jméno a příjmení / Společnost')
			->setRequired('Vyplňte toto pole');

		$form->addValidationEmail('email', 'Emailová adresa', true, true);
		$form->addTextArea('message', 'Zpráva')
			->setRequired('');

		$form->addSubmit('send', 'Zaslat zprávu');
		$form->onValidate[] = function (Form $form): void {
			/** @var ArrayHash $values */
			$values = $form->getUnsafeValues(ArrayHash::class);
			if (mb_strlen($values->message) <= self::MIN_DESCRIPTION_LENGTH) {
				$errorMessage = sprintf(
					'Zadejte prosím alespoň %s znaků do Vaší zprávy.',
					self::MIN_DESCRIPTION_LENGTH
				);
				$form->addError($errorMessage);

				if ($this->isAjax()) {
					$this->redrawControl('contactForm');
				}
			}
		};
		$form->onError[] = function (Form $form): void {
			if ($this->isAjax()) {
				$this->redrawControl('contactForm');
			}
		};

		$form->onSuccess[] = function (Form $form, ArrayHash $values) {
			try {
				$this->ticketManager->sendFromWeb(
					$values->email,
					$values->name,
					$values->message,
					$this->getHttpRequest()->getRemoteAddress()
				);
				$this->flashSuccess('Vaší zprávu jsme přijali');
				$form->reset();
			} catch (\Exception $exception) {
				$form->addError('Zprávu se nepodařilo odeslat. Zkuste to prosím později.');
				Debugger::log($exception);
			} finally {
				if ($this->isAjax()) {
					$this->redrawControl('contactForm');
				} else {
					$this->redirect('this');
				}
			}
		};

		return $form;
	}
}
