<?php

declare(strict_types=1);


namespace App\MessageBus;

use App\Database\Entity\User;
use App\Database\EntityManager;
use App\MessageBus\Stamp\UserStamp;
use App\Security\Identity;
use App\Security\SecurityUser;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;

class UserMiddleware implements MiddlewareInterface
{
	public function __construct(
		private EntityManager $entityManager,
		private SecurityUser  $user
	) {
	}

	final public function handle(Envelope $envelope, StackInterface $stack): Envelope
	{
		$logoutAfter = false;
		try {
			if (!$this->user->isLoggedIn()) {
				/** @var UserStamp[] $userStamps */
				$userStamps = $envelope->all(UserStamp::class);

				foreach ($userStamps as $userStamp) {
					/** @var User $user */
					$user = $this->entityManager->getUserRepository()->findOneBy(['id' => $userStamp->getUserId()]);
					$this->user->login(
						new Identity($userStamp->getUserId(), [$user->getRole()], ['email' => $user->getEmail()])
					);
					$logoutAfter = true;
				}
			}
			$envelope = $stack->next()->handle($envelope, $stack);
		} catch (\InvalidArgumentException $e) {
			throw new UnrecoverableMessageHandlingException($e->getMessage(), 0, $e);
		}
		if ($logoutAfter && $this->user->isLoggedIn()) {
			$this->user->logout();
		}
		return $envelope;
	}
}
