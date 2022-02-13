<?php

declare(strict_types=1);


namespace App\Mailing;

use Contributte\Mailing\IMailBuilderFactory;
use Contributte\Mailing\IMailSender;
use Contributte\Mailing\IMailTemplateFactory;

class MailBuilderFactory implements IMailBuilderFactory
{
	/**
	 * @param string $senderMail
	 * @param string $senderNam
	 * @param string[] $bcc
	 * @param string $replyTo
	 * @param IMailSender $sender
	 * @param IMailTemplateFactory $templateFactory
	 */
	public function __construct(
		private string               $senderMail,
		private array                $bcc,
		private string               $replyTo,
		private IMailSender          $sender,
		private IMailTemplateFactory $templateFactory
	) {
	}

	public function create(): MailBuilder
	{
		$mail = new MailBuilder($this->sender);
		$mail->setTemplate($this->templateFactory->create());
		$mail->setFrom($this->senderMail);
		if ($this->senderMail !== $this->replyTo) {
			$mail->addReplyTo($this->replyTo);
		}
		foreach ($this->bcc as $bcc) {
			$mail->addBcc($bcc);
		}
		return $mail;
	}
}
