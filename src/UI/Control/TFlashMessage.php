<?php

declare(strict_types=1);


namespace App\UI\Control;

use App\Modules\Base\BasePresenter;
use Nette\HtmlStringable;
use stdClass;

/**
 * @mixin BasePresenter
 */
trait TFlashMessage
{
	/**
	 * @param string $type
	 * @internal
	 */
	public function flashMessage(\Nette\HtmlStringable|\stdClass|string $message, $type = 'info'): stdClass
	{
		if ($this->isAjax()) {
			$this->redrawControl('flashes');
		}

		return parent::flashMessage($message, $type);
	}

	public function flashInfo(string $message): stdClass
	{
		return $this->flashMessage($message, 'info');
	}

	public function flashSuccess(string $message): stdClass
	{
		return $this->flashMessage($message, 'success');
	}

	public function flashWarning(string $message): stdClass
	{
		return $this->flashMessage($message, 'warning');
	}

	public function flashError(string $message): stdClass
	{
		return $this->flashMessage($message, 'danger');
	}
}
