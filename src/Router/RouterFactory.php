<?php

declare(strict_types=1);

namespace App\Router;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
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
		$list->addRoute('/app/first-settings', 'Home:settings');
		$list->addRoute('/app/<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}

	protected function buildApi(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Api');
		//todo pro instalaci doplnku by bylo potreba udelat univerzalni routu
		$list->addRoute('/api/shoptet/confirm-installation', 'Shoptet:installation');
		$list->addRoute('/api/<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}

	protected function buildShoptet(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Shoptet');
		$list->addRoute('/app/shoptet/<presenter>/<action>[/<id>]', 'Home:list');

		return $router;
	}

	protected function buildFront(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Front');
		$list->addRoute('/informace-o-doplnku', 'Home:info');
		//todo chybi jeste obecny front
		$list->addRoute('/<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}
}
