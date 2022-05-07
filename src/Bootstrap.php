<?php

declare(strict_types=1);

namespace App;

use Contributte\Bootstrap\ExtraConfigurator;
use Nette\Bootstrap\Configurator;
use Nette\DI\Compiler;
use Tracy\Debugger;

class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new ExtraConfigurator();

		$configurator->onCompile[] = function (ExtraConfigurator $configurator, Compiler $compiler): void {
			// Add env variables to config structure
			$compiler->addConfig(['parameters' => $configurator->getEnvironmentParameters()]);
		};

		$configurator->setEnvDebugMode();

		$configurator->enableTracy(__DIR__ . '/../var/log');
		$configurator->setTempDirectory(__DIR__ . '/../var/temp');
		$configurator->setDebugMode(true);
		$configurator->setFileDebugMode();

		Debugger::$errorTemplate = __DIR__ . '/resources/tracy/500.phtml';

		$configurator->addStaticParameters([
			'rootDir' => realpath(__DIR__ . '/..'),
			'appDir' => __DIR__,
			'wwwDir' => realpath(__DIR__ . '/../public'),
		]);


		if (getenv('NETTE_ENV', true) === 'dev') {
			$configurator->addConfig(__DIR__ . '/../config/env/dev.neon');
		} else {
			$configurator->addConfig(__DIR__ . '/../config/env/prod.neon');
		}

		$configurator->addConfig(__DIR__ . '/../config/config.local.neon');


		return $configurator;
	}

	public static function bootForTests(): Configurator
	{
		$configurator = self::boot();
		\Tester\Environment::setup();
		return $configurator;
	}
}
