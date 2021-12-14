<?php

namespace App\Mailing;

use Aws\Sdk;
use Aws\Ses\SesClient;
use Nette\Mail\Mailer;
use Nette\Mail\Message;

final class SesMailer implements Mailer
{
	public SesClient $sesClient;

	public function __construct(
		private string                  $mailFrom,
		Sdk                             $sdk,
		private SesMailRequestInterface $mailRequest
	) {
		$this->sesClient = $sdk->createSes();
	}

	/**
	 * @param Message $mail
	 */
	public function send($mail): void
	{
		$request = $this->mailRequest->getRequestData($mail, $this->mailFrom);
		bdump($this->sesClient->sendEmail($request));
	}
}
