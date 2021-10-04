<?php

declare(strict_types=1);

namespace App\Modules\Front\Sign;

use App\Api\ClientInterface;
use App\Application;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\DTO\Shoptet\AccessToken;
use App\Exception\Runtime\AuthenticationException;
use App\Facade\UserRegistrationFacade;
use App\Modules\Front\BaseFrontPresenter;
use App\Security\Identity;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Doctrine\ORM\NoResultException;
use Nette\Application\Attributes\Persistent;
use Nette\Application\LinkGenerator;
use Nette\DI\Attributes\Inject;
use Nette\Http\Url;
use Nette\Utils\ArrayHash;
use Ramsey\Uuid\Uuid;

/**
 * @method SecurityUser getUser()
 */
final class SignPresenter extends BaseFrontPresenter
{
	#[Persistent]
	public ?string $backlink = null;

	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public UserRegistrationFacade $userRegistrationFacade;

	#[Inject]
	public ClientInterface $client;

	#[Inject]
	public LinkGenerator $linkGenerator;

	#[Inject]
	public EntityManager $entityManager;


	public function checkRequirements(mixed $element): void
	{
	}

	protected function createComponentOauth()
	{
		$form = $this->formFactory->create();

		$form->addText('url', 'eshop url')
			->setDefaultValue('shoptet.helppc.cz');

		$form->addSubmit('login');

		$form->onSuccess[] = function (Form $form, ArrayHash $values) {

			$url = new Url();
			$url->setScheme('https');
			$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $values->url));
			$url->setPath('action/OAuthServer/');
			$this->getSession('oauth')->set('oauthServer', $url->getAbsoluteUrl());
			$state = Uuid::uuid4()->toString();
			$this->getSession('oauth')->set('state', $state);

			$url->setPath('action/OAuthServer/authorize');
			$url->setQueryParameter('client_id', $this->client->getClientId());
			$url->setQueryParameter('scope', 'basic_eshop');
			$url->setQueryParameter('state', $state);
			$url->setQueryParameter('response_type', 'code');
			$url->setQueryParameter('redirect_uri', $this->linkGenerator->link(Application::DESTINATION_OAUTH_CONFIRM));
			$this->redirectUrl($url->getAbsoluteUrl());
		};
		return $form;
	}

	public function actionOauthConfirm(?string $code, ?string $state)
	{
		$storedState = $this->getSession('oauth')->get('state');
		if ($storedState !== $state) {
			$this->flashError('NEco bylo spatne'); //todo
			$this->redirect('in');
		}
		$url = new Url($this->getSession('oauth')->get('oauthServer'));
		/** @var AccessToken $accessToken */
		$accessToken = $this->client->getOauthAccessToken($code, $url);

		$eshopInfo = $this->client->getEshopInfoFromAccessToken($accessToken, $url);

		try {
			$projectEntity = $this->entityManager->getRepository(Project::class)
				->createQueryBuilder('p')
				->innerJoin('p.users', 'u')
				->addSelect('u')
				->where('p.eshopId = :eshopId')
				->setParameter('eshopId', $eshopInfo->project->id)
				->getQuery()->getSingleResult();
		} catch (NoResultException) {
			$this->flashError('Neni tu!'); //todo
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}

		$userEntity = $projectEntity->getUsers()->filter(fn(User $user) => $user->getEmail() === $eshopInfo->user->email)
			->first();

		if (!$userEntity instanceof User) {
			$userEntity = $this->userRegistrationFacade->createUser(
				$eshopInfo->user->email,
				$projectEntity
			);
			$this->entityManager->flush();
			$this->entityManager->refresh($userEntity);
		}

		$userIdentity = new Identity($eshopInfo->project->id, [User::ROLE_USER],
			array_merge(
				[
					'email' => $eshopInfo->user->email,
					'name' => $eshopInfo->user->name,
					'projectId' => $eshopInfo->project->id,
					'projectName' => $eshopInfo->project->name,
					'projectUrl' => $eshopInfo->project->url,
				],
				[
					'userEntity' => $userEntity,
					'projectEntity' => $projectEntity,
				]
			)
		);

		$this->getUser()->setExpiration(sprintf('%s minutes', $accessToken->getExpiresInMinutes()), false);
		$this->getUser()->login($userIdentity);
		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}

	public function actionIn(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
		}
	}

	public function actionOut(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->getUser()->logout(true);
			$this->flashSuccess('_front.sign.out.success');
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_OUT);
	}

	protected function createComponentLoginForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addValidationEmail('email');
		$form->addPassword('password')
			->setRequired(true);
		$form->addCheckbox('remember')
			->setDefaultValue(true);
		$form->addSubmit('submit');
		$form->onSuccess[] = [$this, 'processLoginForm'];

		return $form;
	}

	public function processLoginForm(Form $form, ArrayHash $values): void
	{
		try {
			$this->getUser()->setExpiration($values->remember ? '14 days' : '20 minutes');
			$this->getUser()->login($values->email, $values->password);
		} catch (AuthenticationException $e) {
			$form->addError('Invalid username or password');

			return;
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}
}
