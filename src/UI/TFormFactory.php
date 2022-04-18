<?php

declare(strict_types=1);

namespace App\UI;

trait TFormFactory
{
	private ?FormFactory $formFactory = null;

	public function injectFormFactory(FormFactory $formFactory): void
	{
		$this->formFactory = $formFactory;
	}

	protected function getFormFactory(): FormFactory
	{
		return $this->formFactory;
	}
}
