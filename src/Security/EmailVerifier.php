<?php

declare(strict_types=1);


namespace App\Security;

use App\Application;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\LogicException;
use Contributte\Mailing\IMailBuilderFactory;
use Nette\Http\IRequest;
use Nette\Utils\Strings;
use Troidcz\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerifier
{
	public function __construct(
		private VerifyEmailHelperInterface $verifyEmailHelper,
		private IMailBuilderFactory $mailBuilderFactory,
		private EntityManager $entityManager
	) {
	}

	public function sendEmailConfirmation(User $user): void
	{
		$signatureComponents = $this->verifyEmailHelper->generateSignature(
			Strings::trim(Application::DESTINATION_ACTIVATION, ':'),
			$user->getGuid()->toString(),
			$user->getEmail()
		);

		$mail = $this->mailBuilderFactory->create();
		$mail->setParameters([
			'user' => $user,
			'signedUrl' => $signatureComponents->getSignedUrl(),
			'expiresAt' => $signatureComponents->getExpiresAt(),
		])->addTo(
			email: $user->getEmail(),
			name: $user->getFullname()
		)
			->setSubject('User registration confirmation')
			->setTemplateFile(__DIR__ . '/../resources/mail/signUp/signUp.latte')
			->send();
	}

	public function handleEmailConfirmation(IRequest $request, User $user): void
	{
		if ($user->isActivated() || $user->isBlocked()) {
			throw new LogicException('User was already blocked or activated');
		}
		$this->verifyEmailHelper->validateRequestEmailConfirmation(
			request: $request,
			userId: $user->getGuid()->toString(),
			userEmail: $user->getEmail()
		);

		$user->activate();

		$this->entityManager->persist($user);
		$this->entityManager->flush();
	}
}
