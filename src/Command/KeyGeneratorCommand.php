<?php

declare(strict_types=1);

namespace App\Command;

use ParagonIE\Halite\KeyFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class KeyGeneratorCommand extends Command
{
	/** @var ?string */
	protected static $defaultName = 'tk:key:generate';

	public function __construct()
	{
		parent::__construct(null);
	}

	protected function configure(): void
	{
		$this
			->setName(static::$defaultName)
			->setDescription('Generate new encryption key pair');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$encKey = KeyFactory::generateEncryptionKeyPair();
		$output->writeln('');
		$output->writeln('<info>Private key:</info>');
		$output->writeln(KeyFactory::export($encKey->getSecretKey())->getString());
		$output->writeln('');
		$output->writeln('<info>Public key:</info>');
		$output->writeln(KeyFactory::export($encKey->getPublicKey())->getString());
		return 0;
	}
}
