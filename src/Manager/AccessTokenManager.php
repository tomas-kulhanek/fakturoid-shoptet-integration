<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\AccessToken;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Exception\RuntimeException;
use App\Mapping\EntityMapping;
use App\Security\SecretVault\ISecretVault;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Lock\LockFactory;

class AccessTokenManager
{
	public function __construct(
		private string $partnerProjectUrl,
		private EntityManager $entityManager,
		private ISecretVault $secretVault,
		private EntityMapping $entityMapping,
		private Client $client,
		private LockFactory $lockFactory,
		private int $maxAccessTokens = 4
	) {
	}

	public function getNewAccessToken(Project $project): \App\DTO\Shoptet\AccessToken
	{
		/** @var \App\DTO\Shoptet\AccessToken $response */
		$response = $this->entityMapping->createEntity(
			$this->client->request(
				method: 'GET',
				uri: $this->partnerProjectUrl . '/getAccessToken',
				options: [
					RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $this->secretVault->decrypt($project->getAccessToken())],
				]
			)->getBody()->getContents(),
			\App\DTO\Shoptet\AccessToken::class
		);
		if ($response->access_token === null) {
			throw new RuntimeException();
		}
		return $response;
	}

	public function leaseToken(Project $project): AccessToken
	{
		$lock = $this->lockFactory->createLock('shoptet_api_access_token');
		if (!$lock->acquire()) {
			//todo tady se musi asi cekat
		}
		try {
			$tokens = $this->entityManager->getRepository(AccessToken::class)
				->createQueryBuilder('at')
				->where('at.project = :project')
				->andWhere('at.expiresIn > :expiresIn')
				->andWhere('at.invalid = 0')
				->setParameter('project', $project)
				->setParameter('expiresIn', new \DateTimeImmutable())
				->getQuery()->getResult();

			/** @var AccessToken $token */
			foreach ($tokens as $token) {
				if (!$token->isLeased()) {
					$token->setLeased(true);
					$this->entityManager->flush($token);
					return $token;
				}
			}

			if (count($tokens) >= $this->maxAccessTokens) {
				throw new \RuntimeException();
			}
			$accessToken = $this->createNewToken($project);
			$accessToken->setLeased(true);
			$this->entityManager->persist($accessToken);
			$this->entityManager->flush($accessToken);
			return $accessToken;
		} finally {
			$lock->release();
		}
	}

	private function createNewToken(Project $project): AccessToken
	{
		$dtoAccessToken = $this->getNewAccessToken($project);
		return new AccessToken(
			$project,
			$this->secretVault->encrypt($dtoAccessToken->access_token),
			(new \DateTimeImmutable())->modify('+ ' . $dtoAccessToken->expires_in . ' sec')
		);
	}

	public function markAsInvalid(AccessToken $accessToken): void
	{
		$accessToken->setInvalid(true);
		$accessToken->setLeased(false);
		$this->entityManager->flush($accessToken);
	}

	public function returnToken(AccessToken $accessToken): void
	{
		$accessToken->setLeased(false);
		$this->entityManager->flush($accessToken);
	}
}
