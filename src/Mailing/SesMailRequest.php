<?php

namespace App\Mailing;

use App\Exception\SesNoRecipientsException;
use Nette\Mail\Message;

class SesMailRequest implements SesMailRequestInterface
{
	public function __construct(
		private string $source,
		private string $configSet,
		private string $charset = 'utf8'
	) {
	}

	/**
	 * @param Message $mail
	 * @param string $senderName
	 * @return array<string, mixed>
	 * @throws SesNoRecipientsException
	 */
	public function getRequestData(Message $mail, string $senderName): array
	{
		// SES request body
		$request = [];
		$sender = $mail->getHeader('From');
		bdump($sender);
		$senderMail = $this->source;
		if (sizeof($sender) > 0) {
			$firstKey = array_key_first($sender);
			if ($sender[$firstKey] !== null && trim($sender[$firstKey]) !== '') {
				$senderName = $sender[$firstKey];
				$senderMail = $firstKey;
			}
		}
		$request['Source'] = '=?UTF-8?B?' . base64_encode($senderName) . '?= <' . $senderMail . '>';
		$request['ConfigurationSetName'] = $this->configSet;

		/** @var array<string, string|null>|null $mailTo */
		$mailTo = $mail->getHeader('To');
		if ($mailTo === null || count($mailTo) < 1) {
			throw new SesNoRecipientsException('No recipients found.');
		}

		$addresses = [];
		foreach ($mailTo as $email => $name) {
			if ($name !== null && trim($name) !== '') {
				$addresses[] = '=?UTF-8?B?' . base64_encode(trim($name)) . '?= <' . $email . '>';
			} else {
				$addresses[] = $email;
			}
		}
		$request['Destination']['ToAddresses'] = $addresses;

		/** @var array<string, string|null>|null $bccs */
		$bccs = $mail->getHeader('Bcc');
		if (count($bccs) > 0) {
			$bccAddresses = [];
			foreach ($bccs as $email => $name) {
				if ($name !== null && trim($name) !== '') {
					$bccAddresses[] = '=?UTF-8?B?' . base64_encode(trim($name)) . '?= <' . $email . '>';
				} else {
					$bccAddresses[] = $email;
				}
			}
			$request['Destination']['BccAddresses'] = $bccAddresses;
		}

		/** @var array<string, string|null>|null $replyTo */
		$replyTo = $mail->getHeader('Reply-To');
		if ($replyTo !== null) {
			$replyToAddresses = [];
			foreach ($replyTo as $replyToEmail => $replyToName) {
				if ($replyToName !== null && trim($replyToName) !== '') {
					$replyToAddresses[] = '=?UTF-8?B?' . base64_encode(trim($replyToName)) . '?= <' . $replyToEmail . '>';
				} else {
					$replyToAddresses[] = $replyToEmail;
				}
			}
			$request['ReplyToAddresses'] = $replyToAddresses;
		}

		// Subject
		$request['Message']['Subject'] = [
			'Data' => $mail->getSubject() ?? '',
			'Charset' => $this->charset,
		];

		// Body
		$htmlBody = $mail->getHtmlBody();
		if (strlen($htmlBody) > 0) {
			$request['Message']['Body']['Html'] = [
				'Data' => $htmlBody,
				'Charset' => $this->charset,
			];
		}
		$textBody = $mail->getBody();
		if (strlen($textBody) > 0) {
			$request['Message']['Body']['Text'] = [
				'Data' => $textBody,
				'Charset' => $this->charset,
			];
		}
		bdump($request);
		return $request;
	}
}
