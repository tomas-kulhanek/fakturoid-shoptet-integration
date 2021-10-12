<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\DBAL\MultiDbConnectionWrapper;
use App\MessageBus\Stamp\EshopStamp;
use Doctrine\DBAL\Connection;
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
		private Connection $connection
	) {
	}

	final public function handle(Envelope $envelope, StackInterface $stack): Envelope
	{
		try {
			/** @var EshopStamp[] $eshopStamps */
			$eshopStamps = $envelope->all(EshopStamp::class);
			foreach ($eshopStamps as $eshopStamp) {
				$this->connection->selectDatabase($eshopStamp->getEshopId());
			}
			$envelope = $stack->next()->handle($envelope, $stack);
		} catch (\InvalidArgumentException $e) {
			throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
		}
		return $envelope;
	}
}
