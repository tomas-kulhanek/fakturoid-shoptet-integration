<?php

declare(strict_types=1);

namespace App\Command;

use App\Security\SecretVault\ISecretVault;
use ParagonIE\Halite\KeyFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VaultEncryptCommand extends Command
{
	/** @var ?string */
	protected static $defaultName = 'tk:vault:encrypt';
	private ISecretVault $secretVault;

	public function __construct(ISecretVault $secretVault)
	{
		parent::__construct(null);
		$this->secretVault = $secretVault;
	}

	protected function configure(): void
	{
		$this
			->setName(static::$defaultName)
			->addArgument('secret', InputArgument::REQUIRED)
			->setDescription('Encrypt secret');
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		/** @var string $secret */
		$secret = $input->getArgument('secret');
		$output->writeln(sprintf('Encrypted: %s', $this->secretVault->encrypt($secret)));
		return 0;
	}
}
