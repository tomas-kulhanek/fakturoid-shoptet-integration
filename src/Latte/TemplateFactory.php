<?php

declare(strict_types=1);

namespace App\Latte;

use App\Security\SecurityUser;
use Nette\Application\UI\Control;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Bridges\ApplicationLatte\LatteFactory;
use Nette\Bridges\ApplicationLatte\Template;
use Nette\Bridges\ApplicationLatte\TemplateFactory as NetteTemplateFactory;
use Nette\Caching\Storage;
use Nette\Http\IRequest;

final class TemplateFactory extends NetteTemplateFactory
{
	public function __construct(
		private string       $projectName,
		private string       $projectMail,
		private string       $shoptetAddonUrl,
		private LatteFactory $latteFactory,
		IRequest             $httpRequest,
		private SecurityUser $user,
		Storage              $cacheStorage,
		string               $templateClass = null,
	) {
		parent::__construct($latteFactory, $httpRequest, $user, $cacheStorage, $templateClass);
	}

	public function createTemplate(Control $control = null, string $class = null): \Nette\Bridges\ApplicationLatte\DefaultTemplate
	{
		/** @var DefaultTemplate $template */
		$template = parent::createTemplate($control);
		// Remove default $template->user for prevent misused
		unset($template->user);

		$parameters = $template->getParameters();
		if (isset($parameters['user'])) {
			unset($parameters['user']);
		}

		$template->setParameters(
			$parameters + [
				'_user' => $this->user,
				'_shoptetAddonUrl' => $this->shoptetAddonUrl,
				'_template' => $template,
				'_projectMail' => $this->projectMail,
				'_projectName' => $this->projectName,
				'_filters' => new FilterExecutor($this->latteFactory->create()),
			]
		);

		return $template;
	}
}
