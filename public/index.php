<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
App\Bootstrap::boot()
	->createContainer()
	->getByType(Contributte\Middlewares\Application\IApplication::class)
	->run();
