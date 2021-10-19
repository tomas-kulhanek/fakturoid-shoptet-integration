<?php declare(strict_types=1);


namespace App\Manager;

use App\Database\Entity\User;
use Maknz\Slack\Client;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

class TicketManager
{

	public function __construct(
		private Client $slackClient,
		private Mailer $mailer
	)
	{
	}

	public function send(User $user, string $message)
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
		$mail = new Message();
		$mail->setBody($message);
		$mail->addReplyTo($user->getEmail(), $user->getName());
		$mail->addTo('jsem@tomaskulhanek.cz', 'Tomáš Kulhánek');
		$mail->setFrom('jsem@tomaskulhanek.cz', 'Tomáš Kulhánek');
		$mail->setSubject('Kontaktní formulář');
		$this->mailer->send($mail);
	}
}
