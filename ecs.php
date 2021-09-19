<?php

// ecs.php
use PhpCsFixer\Fixer\ArrayNotation\ArraySyntaxFixer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\EasyCodingStandard\ValueObject\Set\SetList;
use Symplify\EasyCodingStandard\ValueObject\Option;

return static function (ContainerConfigurator $containerConfigurator): void {
	// A. standalone rule
	$services = $containerConfigurator->services();

	$parameters = $containerConfigurator->parameters();
	// alternative to CLI arguments, easier to maintain and extend
	$parameters->set(Option::PATHS, [__DIR__ . '/src', __DIR__ . '/tests']);
	$services->set(ArraySyntaxFixer::class)
		->call('configure', [[
			'syntax' => 'short',
		]]);

	// scan other file extendsions; [default: [php]]
	$parameters->set(Option::FILE_EXTENSIONS, ['php', 'phpt']);

	// B. full sets
	$containerConfigurator->import(SetList::PSR_12);

	// indent and tabs/spaces
	// [default: spaces]
	$parameters->set(Option::INDENTATION, 'tab');

	// [default: PHP_EOL]; other options: "\n"
	$parameters->set(Option::LINE_ENDING, "\n");
};
