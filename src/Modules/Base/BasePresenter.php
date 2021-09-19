<?php

declare(strict_types=1);


namespace App\Modules\Base;

use App\Latte\TemplateProperty;
use App\Security\SecurityUser;
use App\UI\Control\TFlashMessage;
use App\UI\Control\TModuleUtils;
use Contributte\Application\UI\Presenter\StructuredTemplates;
use Nette\Application\UI\Presenter;
use vavo\EncoreLoader\EncoreLoaderTrait;

/**
 * @property-read TemplateProperty $template
 * @property-read SecurityUser $user
 */
abstract class BasePresenter extends Presenter
{
	use StructuredTemplates;
	use TFlashMessage;
	use TModuleUtils;
	use EncoreLoaderTrait;
}
