<?php

declare(strict_types=1);

namespace App\Router;

use App\Security\SecurityUser;
use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{

	public function __construct(
		private SecurityUser $user,
		private string       $domain = 'dev'
	)
	{
	}

	public function create(): RouteList
	{
		$router = new RouteList();

		$this->buildApi($router);
		$this->buildShoptet($router);
		$this->buildApp($router);
		$this->buildFront($router);

		return $router;
	}

	protected function buildApp(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('App');
		$list[] = (new AppRouter('/<projectId  \d+>/app/first-settings', 'Home:settings'))->setUser($this->user);
		$list[] = (new AppRouter('/<projectId  \d+>/app/<presenter>/<action>[/<id>]', 'Home:default'))->setUser($this->user);

		return $router;
	}

	protected function buildApi(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Api');
		//todo pro instalaci doplnku by bylo potreba udelat univerzalni routu
		$list[] = new Route('/api/shoptet/confirm-installation', 'Shoptet:installation');
		$list[] = (new AppRouter('/<projectId  \d+>api/<presenter>/<action>[/<id>]', 'Home:default'))->setUser($this->user);

		return $router;
	}

	protected function buildShoptet(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Shoptet');
		$list[] = (new AppRouter('/<projectId  \d+>/app/shoptet/<presenter>/<action>[/<id>]', 'Home:list'))->setUser($this->user);

		return $router;
	}

	protected function buildFront(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Front');
		//todo chybi jeste obecny front
		$list[] = new Route('/<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}
}
