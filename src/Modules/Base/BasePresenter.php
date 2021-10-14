<?php

declare(strict_types=1);


namespace App\Modules\Base;

use App\Database\EntityManager;
use App\Latte\TemplateProperty;
use App\Security\SecurityUser;
use App\UI\Control\TFlashMessage;
use App\UI\Control\TModuleUtils;
use Contributte\Application\UI\Presenter\StructuredTemplates;
use Contributte\Translation\LocalesResolvers\Session;
use Nette\Application\Attributes\Persistent;
use Nette\Application\UI\Presenter;
use Nette\DI\Attributes\Inject;
use Nette\Localization\Translator;
use vavo\EncoreLoader\EncoreLoaderTrait;

/**
 * @property-read TemplateProperty $template
 * @property-read SecurityUser $user
 * @method SecurityUser getUser()
 */
abstract class BasePresenter extends Presenter
{
	use StructuredTemplates;
	use TFlashMessage;
	use TModuleUtils;
	use EncoreLoaderTrait;

	#[Inject]
	public Translator $translator;

	#[Inject]
	public Session $translatorSessionResolver;

	#[Inject]
	public EntityManager $_em;

	public function handleChangeLocale(string $locale): void
	{
		$this->translatorSessionResolver->setLocale($locale);
		$this->getUser()->getUserEntity()->setLanguage($locale);
		$this->_em->flush($this->getUser()->getUserEntity());
		$this->redirect('this');
	}

	public function getTranslator(): Translator
	{
		return $this->translator;
	}
}
