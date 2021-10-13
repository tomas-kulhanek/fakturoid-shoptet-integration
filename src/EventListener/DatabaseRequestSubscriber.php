<?php

declare(strict_types=1);


namespace App\EventListener;

use App\DBAL\MultiDbConnectionWrapper;
use Contributte\Events\Extra\Event\Application\RequestEvent;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DatabaseRequestSubscriber implements EventSubscriberInterface
{
	/**
	 * @param MultiDbConnectionWrapper $connectionWrapper
	 * @param \Nette\Database\Connection $coreConnection
	 */
	public function __construct(
		private Connection                 $connectionWrapper,
		private \Nette\Database\Connection $coreConnection
	) {
	}

	public static function getSubscribedEvents(): array
	{
		return [RequestEvent::class => 'onLog'];
	}

	public function onLog(RequestEvent $event): void
	{
		$projectId = (int) $event->getRequest()->getParameter('projectId');
		if ($projectId > 0) {
			$result = $this->coreConnection->query('SELECT id FROM sf_projects WHERE eshop_id = ?', $projectId);
			if ($result->getRowCount() < 1) {
				$event->stopPropagation();
				return;
			}
			$this->connectionWrapper->selectDatabase($projectId);
		}
	}
}
