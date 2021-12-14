<?php

declare(strict_types=1);

namespace App\Command;

use App\Security\Passwords;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PasswordGeneratorCommand extends Command
{
	/** @var string */
	protected static $defaultName = 'tk:password:generate';

	public function __construct(
		private ComputerPasswordGenerator $computerPasswordGenerator,
		private Passwords                 $passwords
	) {
		parent::__construct(null);
	}

	protected function configure(): void
	{
		$this
			->setName(static::$defaultName)
			->setDescription('Generate new password');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$password = $this->computerPasswordGenerator->generatePassword();
		$output->writeln('New password: ' . $password);
		$output->writeln('Password hash: ' . $this->passwords->hash($password));
		return 0;
	}
}
