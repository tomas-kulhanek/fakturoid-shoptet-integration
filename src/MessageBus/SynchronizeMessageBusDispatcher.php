<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\Database\Entity\Shoptet\Project;
use App\MessageBus\Message\Synchronization\CustomerSynchronizationMessage;
use App\MessageBus\Message\Synchronization\InvoiceSynchronizationMessage;
use App\MessageBus\Message\Synchronization\OrderSynchronizationMessage;
use App\MessageBus\Message\Synchronization\ProformaInvoiceSynchronizationMessage;
use App\MessageBus\Stamp\UserStamp;
use App\Security\SecurityUser;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DelayStamp;

class SynchronizeMessageBusDispatcher
{
	public function __construct(
		private MessageBusInterface $messageBus,
		private SecurityUser $user
	) {
	}

	public function dispatchOrder(Project $project, \DateTimeImmutable $from): void
	{
		$this->dispatch(new OrderSynchronizationMessage($project->getEshopId(), $from));
	}

	public function dispatchInvoice(Project $project, \DateTimeImmutable $from): void
	{
		$this->dispatch(new InvoiceSynchronizationMessage($project->getEshopId(), $from));
	}

	public function dispatchProformaInvoice(Project $project, \DateTimeImmutable $from): void
	{
		$this->dispatch(new ProformaInvoiceSynchronizationMessage($project->getEshopId(), $from));
	}

	public function dispatchCustomer(Project $project, \DateTimeImmutable $from): void
	{
		$this->dispatch(new CustomerSynchronizationMessage($project->getEshopId(), $from));
	}

	private function dispatch(
		CustomerSynchronizationMessage|ProformaInvoiceSynchronizationMessage|InvoiceSynchronizationMessage|OrderSynchronizationMessage $message
	): void {
		$stamps = [new DelayStamp(5000)];
		if ($this->user->isLoggedIn()) {
			$stamps[] = new UserStamp($this->user->getId());
		}
		$this->messageBus->dispatch($message, $stamps);
	}
}
