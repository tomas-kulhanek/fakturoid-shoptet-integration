<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\DBAL\MultiDbConnectionWrapper;
use App\MessageBus\Stamp\EshopStamp;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class EshopDatabaseMiddleware implements MiddlewareInterface
{
	/**
	 * @param MultiDbConnectionWrapper $connection
	 */
	public function __construct(
		private Connection      $connection,
		private ManagerRegistry $managerRegistry
	) {
	}

	final public function handle(Envelope $envelope, StackInterface $stack): Envelope
	{
		try {
			if (!$this->connection->isConnected()) {
				$this->connection->connect();
			}
			/** @var EshopStamp[] $eshopStamps */
			$eshopStamps = $envelope->all(EshopStamp::class);
			foreach ($eshopStamps as $eshopStamp) {
				$this->connection->selectDatabase($eshopStamp->getEshopId());
			}
			$envelope = $stack->next()->handle($envelope, $stack);
			$this->connection->close();

			$manager = $this->managerRegistry->getManager('default');
			$manager->clear();
			$this->managerRegistry->resetManager('default');
			//$this->connection->connectBack();
		} catch (\InvalidArgumentException $e) {
			throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
		}
		return $envelope;
	}
}
