<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Nette\Set\NetteSetList;
use Rector\Set\ValueObject\LevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
	// get parameters
	$parameters = $containerConfigurator->parameters();
	$parameters->set(Option::PATHS, [
		__DIR__ . '/src'
	]);

	// Define what rule sets will be applied
	$containerConfigurator->import(LevelSetList::UP_TO_PHP_80);
	$containerConfigurator->import(NetteSetList::NETTE_31);
	$containerConfigurator->import(NetteSetList::NETTE_STRICT);

	// get services (needed for register a single rule)
	$services = $containerConfigurator->services();

	$parameters->set(Option::PHPSTAN_FOR_RECTOR_PATH, __DIR__ . '/phpstan.neon');
	$parameters->set(Option::SKIP, [
		\Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class
	]);

	// register a single rule
	// $services->set(TypedPropertyRector::class);
};
