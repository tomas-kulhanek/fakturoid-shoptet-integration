<?php

declare(strict_types=1);

namespace App\UI;

use App\Utils\FormValidator;
use Nette\Localization\Translator;

class FormFactory implements IFormFactory
{
	public function __construct(
		private Translator $translator,
		private FormValidator $validator
	) {
		$this->validator->setValidatorMessages();
	}

	public function create(bool $csrfProtection = false): Form
	{
		$form = new Form();
		$form->setBootstrapRenderer();
		$form->setTranslator($this->translator);
		$form->addValidator($this->validator);
		if ($csrfProtection === true) {
			$form->addProtection();
		}
		return $form;
	}
}
