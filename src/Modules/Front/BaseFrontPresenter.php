<?php

declare(strict_types=1);

namespace App\Modules\Front;

use App\Api\ClientInterface;
use App\Application;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Exception\OAuth\MissingProject;
use App\Modules\Base\UnsecuredPresenter;
use App\Security\SecurityUser;
use App\UI\FormFactory;
use Doctrine\ORM\NoResultException;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Http\Url;
use Nette\Utils\Json;
use Ramsey\Uuid\Uuid;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
abstract class BaseFrontPresenter extends UnsecuredPresenter
{
	public function __construct(
		protected EntityManager   $entityManager,
		protected ClientInterface $client,
		protected LinkGenerator   $linkGenerator,
		protected FormFactory     $formFactory
	) {
		parent::__construct();
	}

	protected function beforeRender()
	{
		parent::beforeRender();
		$this->getTemplate()->setParameters(['eshopUrls' => $this->getEshopListFromSession()]);
	}

	protected function getFormFactory(): FormFactory
	{
		return $this->formFactory;
	}

	protected function getClient(): ClientInterface
	{
		return $this->client;
	}

	protected function getLinkGenerator(): LinkGenerator
	{
		return $this->linkGenerator;
	}

	protected function getEntityManager(): EntityManager
	{
		return $this->entityManager;
	}

	protected function getOauthUrl(string $shopUrl): Url
	{
		$url = new Url();
		$url->setScheme('https');
		$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $shopUrl));
		$clonedUrl = clone $url;
		$clonedUrl->setScheme('http');

		$qb = $this->getEntityManager()->getRepository(Project::class)
			->createQueryBuilder('p');
		try {
			$projectEntity = $qb
				->where($qb->expr()->like('p.eshopUrl', ':eshopUrl'))
				->orWhere($qb->expr()->like('p.eshopUrl', ':eshopUrl2'))
				->setParameter('eshopUrl', $url->getAbsoluteUrl())
				->setParameter('eshopUrl2', $clonedUrl->getAbsoluteUrl())
				->getQuery()->getSingleResult();
		} catch (NoResultException $exception) {
			$ex = new MissingProject($this->getTranslator()->translate('messages.sign.in.missingShop', ['shop' => $url->getAbsoluteUrl()]), 404, $exception);
			$ex->setShopUrl($url);
			throw $ex;
		}

		$url->setPath('action/OAuthServer/');
		$this->getSession('oauth')->set('oauthServer', $url->getAbsoluteUrl());
		$state = Uuid::uuid4()->toString();
		$this->getSession('oauth')->set('state', $state);

		$url->setPath('action/OAuthServer/authorize');
		$url->setQueryParameter('client_id', $this->getClient()->getClientId());
		$url->setQueryParameter('scope', 'basic_eshop');
		$url->setQueryParameter('state', $state);
		$url->setQueryParameter('response_type', 'code');
		$url2 = new Url($this->getLinkGenerator()->link(Application::DESTINATION_OAUTH_CONFIRM));
		//$url2->setPort(8080); //http://dev.tomaskulhanek.cz:8080/sign/oauth-confirm
		$url->setQueryParameter('redirect_uri', $url2->getAbsoluteUrl());

		return $url;
	}

	/**
	 * @return array<string, string>
	 * @throws \Nette\Utils\JsonException
	 */
	protected function getEshopListFromSession(): array
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
}
