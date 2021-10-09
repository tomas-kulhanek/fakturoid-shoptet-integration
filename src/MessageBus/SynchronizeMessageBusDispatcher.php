<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\Database\Entity\Shoptet\Project;
use App\MessageBus\Message\Synchronization\CustomerSynchronizationMessage;
use App\MessageBus\Message\Synchronization\InvoiceSynchronizationMessage;
use App\MessageBus\Message\Synchronization\OrderSynchronizationMessage;
use App\MessageBus\Message\Synchronization\ProformaInvoiceSynchronizationMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class SynchronizeMessageBusDispatcher
{
	public function __construct(
		private MessageBusInterface $messageBus
	) {
	}

	public function dispatchOrder(Project $project, \DateTimeImmutable $from): void
	{
		$this->messageBus->dispatch(new OrderSynchronizationMessage($project->getEshopId(), $from), [new DelayStamp(5000)]);
	}

	public function dispatchInvoice(Project $project, \DateTimeImmutable $from): void
	{
		$this->messageBus->dispatch(new InvoiceSynchronizationMessage($project->getEshopId(), $from), [new DelayStamp(5000)]);
	}

	public function dispatchProformaInvoice(Project $project, \DateTimeImmutable $from): void
	{
		$this->messageBus->dispatch(new ProformaInvoiceSynchronizationMessage($project->getEshopId(), $from), [new DelayStamp(5000)]);
	}

	public function dispatchCustomer(Project $project, \DateTimeImmutable $from): void
	{
		$this->messageBus->dispatch(new CustomerSynchronizationMessage($project->getEshopId(), $from), [new DelayStamp(5000)]);
	}
}
