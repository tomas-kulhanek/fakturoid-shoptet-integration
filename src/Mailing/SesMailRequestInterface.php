<?php

namespace App\Mailing;

use Nette\Mail\Message;

interface SesMailRequestInterface
{
	/**
	 * @param Message $mail
	 * @param string $senderName
	 * @return array<string, mixed>
	 */
	public function getRequestData(Message $mail, string $senderName): array;
}
