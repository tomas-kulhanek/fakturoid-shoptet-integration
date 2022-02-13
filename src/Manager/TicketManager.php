<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\User;
use App\Mailing\MailBuilderFactory;

class TicketManager
{
	public function __construct(
		private MailBuilderFactory $mailer,
		private string             $recipientMail,
		private string             $recipientName
	) {
	}

	public function sendFromWeb(string $email, string $user, string $message, ?string $userIp): void
	{
		$mail = $this->mailer->create();
		$mail->setTemplateFile(__DIR__ . '/../resources/mail/contactWeb.latte');
		$mail->setParameters([
			'message' => $message,
			'user' => $user,
			'userIp' => $userIp,
			'email' => $email
		]);
		$mail->addReplyTo($email, $user);
		$mail->addTo($this->recipientMail, $this->recipientName);
		$mail->setSubject('Kontaktní formulář z webu');
		$mail->send();
	}

	public function send(User $user, string $message): void
	{
		$mail = $this->mailer->create();
		$mail->setTemplateFile(__DIR__ . '/../resources/mail/contact.latte');
		$mail->setParameters([
			'message' => $message,
			'user' => $user,
		]);
		$mail->addReplyTo($user->getEmail(), $user->getName());
		$mail->addTo($this->recipientMail, $this->recipientName);
		$mail->setSubject('Kontaktní formulář');
		$mail->send();
	}
}
