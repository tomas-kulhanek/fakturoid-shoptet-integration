<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\Database\Entity\Shoptet\Document;
use App\MessageBus\Stamp\UserStamp;
use App\Security\SecurityUser;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;
use Tracy\Debugger;
use Tracy\ILogger;
use function Clue\StreamFilter\remove;

class AccountingBusDispatcher
{
	public function __construct(
		private MessageBusInterface $messageBus,
		private SecurityUser        $user
	) {
	}

	public function dispatch(Document $document): void
	{
		if (php_sapi_name() === 'cli') {
			if ($document->getProject()->getAccountingSyncFrom() !== null && $document->getProject()->getAccountingSyncFrom() > $document->getCreationTime()) {
				Debugger::log(
					sprintf(
						'Document %s was created %s before enabled date %s',
						$document->getCode(),
						$document->getCreationTime()->format('d.m.Y H:i:s'),
						$document->getProject()->getAccountingSyncFrom()->format('d.m.Y H:i:s')
					),
					ILogger::CRITICAL
				);
				return;
			}
		}
		if ($document instanceof \App\Database\Entity\Shoptet\Invoice) {
			$message = new \App\MessageBus\Message\Accounting\Invoice(
				eshopId: $document->getProject()->getEshopId(),
				documentId: $document->getId()
			);
		} elseif ($document instanceof \App\Database\Entity\Shoptet\ProformaInvoice) {
			$message = new \App\MessageBus\Message\Accounting\ProformaInvoice(
				eshopId: $document->getProject()->getEshopId(),
				documentId: $document->getId()
			);
		} elseif ($document instanceof \App\Database\Entity\Shoptet\CreditNote) {
			$message = new \App\MessageBus\Message\Accounting\CreditNote(
				eshopId: $document->getProject()->getEshopId(),
				documentId: $document->getId()
			);
		} else {
			throw new \Exception();
		}
		$stamps = [new DelayStamp(5000)];
		if ($this->user->isLoggedIn()) {
			$stamps[] = new UserStamp($this->user->getId());
		}
		$this->messageBus->dispatch($message, $stamps);
	}
}
