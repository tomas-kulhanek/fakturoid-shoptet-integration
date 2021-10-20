<?php

declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\User;
use App\Mailing\MailBuilderFactory;
use Maknz\Slack\Client;
use Nette\Mail\Message;

class TicketManager
{
	public function __construct(
		private Client $slackClient,
		private MailBuilderFactory $mailer
	) {
	}

	public function send(User $user, string $message): void
	{
		$messageObject = $this->slackClient->createMessage();
		$messageObject->attach([
			'fallback' => $message,
			'text' => $message,
			'author_name' => sprintf('%s - %d', $user->getName(), $user->getProject()->getEshopId()),
			'author_link' => $user->getProject()->getEshopUrl(),
			'fields' => [
				['title' => 'Eshop', 'value' => $user->getProject()->getEshopId()],
				['title' => 'Email', 'value' => $user->getEmail()],
				['title' => 'Time', 'value' => (new \DateTimeImmutable())->format('d.m.Y H:i:s')],
			],
		])->send('New user feedback');
		$mail = $this->mailer->create();
		$mail->setTemplateFile(__DIR__ . '/../resources/mail/contact.latte');
		$mail->setParameters([
			'message' => $message,
			'user' => $user,
		]);
		$mail->addReplyTo($user->getEmail(), $user->getName());
		$mail->addTo('jsem@tomaskulhanek.cz', 'Tomáš Kulhánek');
		$mail->setSubject('Kontaktní formulář');
		$mail->send();
	}
}