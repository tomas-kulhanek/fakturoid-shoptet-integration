<?php

declare(strict_types=1);

namespace App\Latte;

use App\Modules\Base\BasePresenter;
use App\Security\SecurityUser;
use App\UI\Control\BaseControl;
use Nette\Bridges\ApplicationLatte\Template;

/**
 * @property-read SecurityUser $_user
 * @property-read string $_projectName
 * @property-read string $_projectMail
 * @property-read BasePresenter $presenter
 * @property-read BaseControl $control
 * @property-read string $baseUri
 * @property-read string $basePath
 * @property-read array $flashes
 */
final class TemplateProperty extends Template
{
}
