<?php declare(strict_types=1);


namespace App\Manager\Core;


use Nette\Database\Connection;
use Nette\Database\Row;
use Nette\Http\Url;

class ProjectManager
{

	public function __construct(
		private Connection $connection,
	)
	{
	}

	public function getByEshopUrl(string $eshopUrl): ?Row
	{
		$url = new Url();
		$url->setScheme('https');
		$url->setHost(str_replace(['https://', 'http://', '/'], ['', '', ''], $eshopUrl));
		return $this->getProjectByUrl($url);
	}

	public function getByEshopId(int $shopId): ?Row
	{
		return $this->connection->query('SELECT * FROM sf_projects WHERE eshop_id = ?', $shopId)
			->fetch();

	}

	public function getProjectByUrl(Url $url): ?Row
	{
		$clonedUrl = clone $url;
		$clonedUrl->setScheme('http');

		return $this->connection->query('SELECT * FROM sf_projects WHERE eshop_url = ?', $url->getAbsoluteUrl(), 'OR eshop_url = ?', $clonedUrl->getAbsoluteUrl())
			->fetch();

	}
}
