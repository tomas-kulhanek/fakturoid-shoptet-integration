<?php declare(strict_types=1);


namespace App\EventListener;

use App\DBAL\MultiDbConnectionWrapper;
use App\Security\SecurityUser;
use Contributte\Events\Extra\Event\Application\RequestEvent;
use Doctrine\DBAL\Connection;
use Nette\Database\Row;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class DatabaseRequestSubscriber implements EventSubscriberInterface
{

	/**
	 * @param MultiDbConnectionWrapper $connectionWrapper
	 * @param \Nette\Database\Connection $coreConnection
	 * @param SecurityUser $user
	 */
	public function __construct(
		private Connection                 $connectionWrapper,
		private \Nette\Database\Connection $coreConnection,
		private SecurityUser               $user
	)
	{
	}

	public static function getSubscribedEvents(): array
	{
		return [RequestEvent::class => 'onLog'];
	}

	public function onLog(RequestEvent $event): void
	{
		if ($event->getRequest()->getParameter('projectId')) {
			$projectId = (int) $event->getRequest()->getParameter('projectId');
			$result = $this->coreConnection->query('SELECT id FROM sf_projects WHERE eshop_id = ?', $projectId);
			if ($result->getRowCount() < 1) {
				$event->stopPropagation();
				return;
			}
			$this->connectionWrapper->selectDatabase($projectId);
		}

		bdump($event->getRequest());
	}

}
