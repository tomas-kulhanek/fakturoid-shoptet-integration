<?php

declare(strict_types=1);

namespace App\Modules\Front\Sign;

use App\Api\ClientInterface;
use App\Application;
use App\Database\Entity\Shoptet\Project;
use App\Database\Entity\User;
use App\Database\EntityManager;
use App\Exception\Logic\NotFoundException;
use App\Exception\OAuth\MissingProject;
use App\Exception\Runtime\AuthenticationException;
use App\Manager\UserManager;
use App\Modules\Front\BaseFrontPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Doctrine\ORM\NoResultException;
use GuzzleHttp\Exception\ClientException;
use Nette\Application\Attributes\Persistent;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;
use Nette\Http\Url;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Json;
use Ramsey\Uuid\Uuid;
use Tracy\Debugger;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
final class SignPresenter extends BaseFrontPresenter
{
	#[Persistent]
	public ?string $backlink = null;

	#[Inject]
	public Translator $translator;

	#[Inject]
	public FormFactory $formFactory;

	#[Inject]
	public UserManager $userManager;

	#[Inject]
	public EntityManager $entityManager;

	#[Inject]
	public ClientInterface $client;

	#[Inject]
	public LinkGenerator $linkGenerator;

	public function checkRequirements(mixed $element): void
	{
	}

	private function appendEshopUrl(string $eshopUrl): void
	{
		$url = new Url();
		$url->setScheme('https');
		$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $eshopUrl));

		$eshopList = $this->getSession('eshop')->get('urls');
		if ($eshopList === null) {
			$eshopList = [];
		} else {
			$eshopList = (array)Json::decode($eshopList);
		}

		if (!isset($eshopList[$url->getHost()])) {
			$eshopList[$url->getHost()] = $url->getHost();
		}
		if (count($eshopList) > 0) {
			$this->getSession('eshop')->set('urls', Json::encode($eshopList));
		}
	}

	protected function createComponentOauth(): Form
	{
		$form = $this->formFactory->create();

		$form->addText('shopUrl', 'eshop url');

		$form->addSubmit('submit');

		$form->onSuccess[] = function (Form $form, ArrayHash $values): void {
			try {
				$redirectUrl = $this->getOauthUrl($values->shopUrl);
			} catch (MissingProject $exception) {
				$this->flashError($exception->getMessage());
				$this->redirect('this');
			}
			$this->redirectUrl($redirectUrl->getAbsoluteUrl());
		};

		return $form;
	}

	public function actionSso(string $shopUrl): void
	{
		try {
			$redirectUrl = $this->getOauthUrl($shopUrl);
		} catch (MissingProject $exception) {
			$this->flashError($exception->getMessage());
			$this->redirect(Application::DESTINATION_SIGN_IN);
		}
		$this->redirectUrl($redirectUrl->getAbsoluteUrl());
	}

	private function getOauthUrl(string $shopUrl): Url
	{
		$url = new Url();
		$url->setScheme('https');
		$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $shopUrl));
		$clonedUrl = clone $url;
		$clonedUrl->setScheme('http');

		$qb = $this->entityManager->getRepository(Project::class)
			->createQueryBuilder('p');
		try {
			$projectEntity = $qb
				->where($qb->expr()->like('p.eshopUrl', ':eshopUrl'))
				->orWhere($qb->expr()->like('p.eshopUrl', ':eshopUrl2'))
				->setParameter('eshopUrl', $url->getAbsoluteUrl())
				->setParameter('eshopUrl2', $clonedUrl->getAbsoluteUrl())
				->getQuery()->getSingleResult();
		} catch (NoResultException $exception) {
			$ex = new MissingProject($this->translator->translate('messages.sign.in.missingShop', ['shop' => $url->getAbsoluteUrl()]), 404, $exception);
			$ex->setShopUrl($url);
			throw $ex;
		}

		$url->setPath('action/OAuthServer/');
		$this->getSession('oauth')->set('oauthServer', $url->getAbsoluteUrl());
		$state = Uuid::uuid4()->toString();
		$this->getSession('oauth')->set('state', $state);

		$url->setPath('action/OAuthServer/authorize');
		$url->setQueryParameter('client_id', $this->client->getClientId());
		$url->setQueryParameter('scope', 'basic_eshop');
		$url->setQueryParameter('state', $state);
		$url->setQueryParameter('response_type', 'code');
		$url2 = new Url($this->linkGenerator->link(Application::DESTINATION_OAUTH_CONFIRM));
		$url2->setPort(8080);
		$url->setQueryParameter('redirect_uri', $url2->getAbsoluteUrl());

		return $url;
	}

	public function actionOauthConfirm(?string $code, ?string $state): void
	{
		$storedState = $this->getSession('oauth')->get('state');
		if ($storedState !== $state) {
			$this->flashError($this->translator->translate('messages.sign.in.stateMissMatch'));
			$this->redirect('in');
		}

		$url = new Url($this->getSession('oauth')->get('oauthServer'));

		try {
			$accessToken = $this->client->getOauthAccessToken($code, $url);

			$eshopInfo = $this->client->getEshopInfoFromAccessToken($accessToken, $url);
			$this->appendEshopUrl($url->getHost());
		} catch (ClientException $exception) {
			Debugger::log($exception);
			$this->flashError($this->translator->translate('messages.sign.in.shoptetAuthError', ['shop' => $url->getHost()]));
			$this->redirect(Application::DESTINATION_SIGN_IN);
		}


		try {
			$projectEntity = $this->entityManager->getRepository(Project::class)
				->createQueryBuilder('p')
				->innerJoin('p.users', 'u')
				->addSelect('u')
				->where('p.eshopId = :eshopId')
				->setParameter('eshopId', $eshopInfo->project->id)
				->getQuery()->getSingleResult();
		} catch (NoResultException) {
			$this->flashError($this->translator->translate('messages.sign.in.missingShop', ['shop' => $url->getHost()]));
			$this->redirect(Application::DESTINATION_SIGN_IN);
		}

		$userEmail = $eshopInfo->user->email;
		if ($userEmail === 'kulhanek@shoptet.cz') {
			$userEmail = 'jsem@tomaskulhanek.cz';
		}
		$userEntity = $projectEntity->getUsers()->filter(fn (User $user) => $user->getEmail() === $userEmail)
			->first();

		if (!$userEntity instanceof User) {
			$this->flashError($this->translator->translate('messages.sign.in.missingUser', ['shop' => $url->getHost()]));
			$this->redirect(Application::DESTINATION_SIGN_IN);
		}
		$userEntity->setName($eshopInfo->user->name);
		$userEntity->getProject()->setName($eshopInfo->project->name);
		$this->entityManager->flush();

		$this->getUser()->login(
			$userEntity->toIdentity()
		);
		bdump($userEntity->toIdentity());
		$this->translatorSessionResolver->setLocale($userEntity->getLanguage());
		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}

	/**
	 * @return array<string, string>
	 * @throws \Nette\Utils\JsonException
	 */
	private function getEshopListFromSession(): array
	{
		static $eshopList;
		if (isset($eshopList)) {
			return $eshopList;
		}
		$eshopList = $this->getSession('eshop')->get('urls');
		if ($eshopList === null) {
			$eshopList = [];
		} else {
			$eshopList = (array)Json::decode($eshopList);
		}

		return $eshopList;
	}

	public function actionIn(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
		}
		$this->getTemplate()->setParameters(['eshopUrls' => $this->getEshopListFromSession()]);
	}

	public function actionOut(): void
	{
		if ($this->getUser()->isLoggedIn()) {
			$this->getUser()->logout(true);
			$this->flashSuccess($this->translator->translate('messages.sign.out'));
		}

		$this->redirect(Application::DESTINATION_AFTER_SIGN_OUT);
	}

	protected function createComponentSetPasswordForm(): Form
	{
		$form = $this->formFactory->create();
		$form->addPasswords('password', '', '')
			->setRequired(true);
		$form->addSubmit('submit');

		$form->onSuccess[] = [$this, 'processSetPassword'];

		return $form;
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

		$form->addText('web', 'eshop url');

		$form->onSuccess[] = [$this, 'processLoginForm'];

		return $form;
	}

	public function processSetPassword(Form $form, ArrayHash $values): void
	{
		if ($values->password !== $values->passwordAgain) {
			$this->flashSuccess('messages.setPassword.passwordMismatch');
			$this->redirect(Application::DESTINATION_FORCE_CHANGE_PASSWORD);
		}
		$this->userManager->setNewPassword(
			$this->getUser()->getUserEntity(),
			$values->passwordAgain
		);
		$this->flashSuccess(
			$this->getTranslator()->translate('messages.setPassword.success')
		);
		$this->redirect(Application::DESTINATION_APP_HOMEPAGE);
	}

	public function processLoginForm(Form $form, ArrayHash $values): void
	{
		try {
			$this->getUser()->setExpiration('14 days');
			$this->userManager->authenticate(
				$values->web,
				$values->email,
				$values->password
			);
			$this->appendEshopUrl($values->web);
		} catch (AuthenticationException) {
			$this->flashError(
				$this->translator->translate('messages.sign.in.credentialsMissmatch')
			);

			return;
		} catch (NotFoundException) {
			$this->flashError(
				$this->translator->translate('messages.sign.in.missingShop', ['shop' => $values->web])
			);

			return;
		}

		if ($this->getUser()->isLoggedIn() || $this->getUser()->getUserEntity()->isForceChangePassword()) {
			$this->redirect(Application::DESTINATION_FORCE_CHANGE_PASSWORD);
		}
		$this->redirect(Application::DESTINATION_AFTER_SIGN_IN);
	}

	public function actionSetPassword(): void
	{
		if (!$this->getUser()->isLoggedIn() || !$this->getUser()->getUserEntity()->isForceChangePassword()) {
			$this->redirect(Application::DESTINATION_SIGN_IN);
		}
	}
}
