<?php declare(strict_types=1);


namespace App\Router;

use App\Security\SecurityUser;
use Nette;
use Nette\Application\Routers\Route;

class AppRouter extends Route
{

	private SecurityUser $user;

	public function setUser(SecurityUser $user): AppRouter
	{
		$this->user = $user;
		return $this;
	}

	public function constructUrl(array $params, Nette\Http\UrlScript $refUrl): ?string
	{
		if (!isset($params['projectId']) && $this->user->isLoggedIn()) {
			//$params['projectId'] = $this->user->getProjectEntity()->getEshopId();
		}

		return parent::constructUrl($params, $refUrl);
	}
}
