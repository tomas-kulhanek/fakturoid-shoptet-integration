<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\Database\Entity\Shoptet\Document;
use App\MessageBus\Stamp\UserStamp;
use App\Security\SecurityUser;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class AccountingBusDispatcher
{
	public function __construct(
		private MessageBusInterface $messageBus,
		private SecurityUser        $user
	) {
	}

	public function dispatch(Document $document): void
	{
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
