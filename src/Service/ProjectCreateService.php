<?php declare(strict_types=1);


namespace App\Service;


use App\Database\Entity\Shoptet\Project;
use App\DBAL\MultiDbConnectionWrapper;
use App\DTO\Shoptet\ConfirmInstallation;
use App\Security\SecretVault\ISecretVault;
use Nette\Database\Connection;
use Nette\Database\ResultSet;
use Nextras\Migrations\Bridges;
use Nextras\Migrations\Engine\Runner;
use Nextras\Migrations\Entities\Group;
use Nextras\Migrations\IDriver;
use Nextras\Migrations\Extensions;
use Nextras\Migrations\Printers\DevNull;
use Ramsey\Uuid\Uuid;

class ProjectCreateService
{

	/**
	 * @param Connection $connection
	 * @param IDriver $driver
	 * @param MultiDbConnectionWrapper $doctrineConnection
	 * @param string $databaseUserName
	 * @param string $applicationIp
	 */
	public function __construct(
		private Connection                $connection,
		private IDriver                   $driver,
		private \Doctrine\DBAL\Connection $doctrineConnection,
		private ISecretVault              $secretVault,
		private string                    $databaseUserName,
		private string                    $applicationIp
	)
	{
	}

	public function createNewProject(ConfirmInstallation $confirmInstallation): void
	{
		$projectData = [
			'access_token' => $this->secretVault->encrypt($confirmInstallation->access_token),
			'token_type' => $confirmInstallation->token_type,
			'scope' => $confirmInstallation->scope,
			'state' => Project::STATE_NOT_INITIALIZED,
			'eshop_id' => $confirmInstallation->eshopId,
			'eshop_url' => $confirmInstallation->eshopUrl,
			'contact_email' => $confirmInstallation->contactEmail,
			'guid' => Uuid::uuid4()->toString(),
			'created_at' => new \DateTimeImmutable(),
			'last_customer_sync_at' => new \DateTimeImmutable(),
			'last_order_sync_at' => new \DateTimeImmutable(),
			'last_invoice_sync_at' => new \DateTimeImmutable(),
			'last_proforma_sync_at' => new \DateTimeImmutable(),
			'identifier' => substr(sha1(Uuid::uuid4()->toString()), 0, 200),
		];
		$result = $this->connection->query('SELECT id FROM sf_projects WHERE eshop_id = ?', $confirmInstallation->eshopId);
		if ($result->getRowCount() === 0) {
			$this->connection->query('INSERT INTO sf_projects ?', $projectData);
		}

		$this->createNewProjectDatabase($confirmInstallation->eshopId);
	}

	public function createNewProjectDatabase(int $eshopId): void
	{
		/** @var ResultSet $result */
		$result = $this->connection->query('SHOW DATABASES LIKE ?', 'p_' . $eshopId);

		if ($result->getRowCount() === 0) {
			$this->connection->query('CREATE DATABASE p_' . $eshopId);
			$this->connection->query('GRANT ALL PRIVILEGES ON p_' . $eshopId . '.* TO \'' . $this->databaseUserName . '\'@\'' . $this->applicationIp . '\'');
		}
		$this->doctrineConnection->selectDatabase($eshopId);

		$baseDir = __DIR__ . '/../../migrations';

		$runner = new Runner($this->driver, new DevNull());
		$runner->addExtensionHandler('sql', new Extensions\SqlHandler($this->driver));
		$group = new Group;
		$group->name = 'structures';
		$group->directory = "$baseDir/structures";
		$group->dependencies = [];
		$group->enabled = true;
		$runner->addGroup($group);

		$group = new Group;
		$group->name = 'basic-data';
		$group->directory = "$baseDir/basic-data";
		$group->dependencies = ['structures'];
		$group->enabled = true;
		$runner->addGroup($group);
		$runner->run(Runner::MODE_CONTINUE);
	}

}
