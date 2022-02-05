<?php

declare(strict_types=1);

namespace App\Command;

use App\MessageBus\Handler\ProjectCreateHandler;
use Contributte\Mailing\IMailBuilderFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MailTestCommand extends Command
{
	/** @var ?string */
	protected static $defaultName = 'tk:test:mail';

	public function __construct(
		private IMailBuilderFactory $mailBuilderFactory
	) {
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
		$email = ProjectCreateHandler::SUPERADMIN_MAIL;
		$message = $this->mailBuilderFactory->create();
		$message->setSubject('TEST');
		$message->addTo($email);
		$message->getMessage()->addAttachment('/var/www/var/log/info--2022-02-05--16-49--36fe1eb9f8.html', null, 'application/html');
		$message->setTemplateFile(__DIR__ . '/../resources/mail/installation.latte');
		$message->setParameters([
			'showAccounts' => false,
			'projectUrl' => 'shoptet.tomaskulhanek.cz',
			'email' => $email,
			'password' => 'asdasd',
			'autoLoginUrl' => ''
		]);
		$message->send();
		return 0;
	}
}
