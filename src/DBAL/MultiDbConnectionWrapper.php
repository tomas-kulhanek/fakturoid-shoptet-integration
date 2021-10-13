<?php

declare(strict_types=1);


namespace App\DBAL;

use Doctrine\Common\EventManager;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;

class MultiDbConnectionWrapper extends Connection
{
	private string $originalDatabaseName;

	public function __construct(array $params, Driver $driver, ?Configuration $config = null, ?EventManager $eventManager = null)
	{
		$this->originalDatabaseName = $params['dbname'];
		parent::__construct($params, $driver, $config, $eventManager);
	}

	public function selectDatabase(int $projectId): void
	{
		if ($this->isConnected()) {
			$this->close();
		}
		$params = $this->getParams();
		$params['dbname'] = sprintf('p_%d', $projectId);
		parent::__construct($params, $this->_driver, $this->_config, $this->_eventManager);
	}

	public function connectBack(): void
	{
		if ($this->isConnected()) {
			$this->close();
		}
		$params = $this->getParams();
		$params['dbname'] = $this->originalDatabaseName;
		parent::__construct($params, $this->_driver, $this->_config, $this->_eventManager);
	}
}
