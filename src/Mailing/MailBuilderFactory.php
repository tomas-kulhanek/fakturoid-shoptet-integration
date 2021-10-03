<?php

declare(strict_types=1);


namespace App\Mailing;

use Contributte\Mailing\IMailBuilderFactory;
use Contributte\Mailing\IMailSender;
use Contributte\Mailing\IMailTemplateFactory;

class MailBuilderFactory implements IMailBuilderFactory
{
	public function __construct(
		private IMailSender $sender,
		private IMailTemplateFactory $templateFactory
	) {
	}

	public function create(): MailBuilder
	{
		$mail = new MailBuilder($this->sender);
		$mail->setTemplate($this->templateFactory->create());
		$mail->setFrom(from: 'test@test.cz', fromName: 'Testovac');
		return $mail;
	}
}
