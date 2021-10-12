<?php

declare(strict_types=1);

namespace App\Router;

use Nette\Application\Routers\Route;
use Nette\Application\Routers\RouteList;

final class RouterFactory
{
	public function __construct(
		private string $subdomain
	) {
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
		$list[] = new Route('//<projectId  \d+>.' . $this->subdomain . '.%domain%/app/first-settings', 'Home:settings');
		$list[] = new Route('//<projectId  \d+>.' . $this->subdomain . '.%domain%/app/<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}

	protected function buildApi(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Api');
		//todo pro instalaci doplnku by bylo potreba udelat univerzalni routu
		$list[] = new Route('//' . $this->subdomain . '.%domain%/api/shoptet/confirm-installation', 'Shoptet:installation');
		$list[] = new Route('//<projectId  \d+>.' . $this->subdomain . '.%domain%/api/<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}

	protected function buildShoptet(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Shoptet');
		$list[] = new Route('//<projectId  \d+>.' . $this->subdomain . '.%domain%/app/shoptet/<presenter>/<action>[/<id>]', 'Home:list');

		return $router;
	}

	protected function buildFront(RouteList $router): RouteList
	{
		$router[] = $list = new RouteList('Front');
		//todo chybi jeste obecny front
		$list[] = new Route('//' . $this->subdomain . '.%domain%/<presenter>/<action>[/<id>]', 'Home:default');

		return $router;
	}
}
