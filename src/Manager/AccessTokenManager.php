<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\AccessToken;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Exception\RuntimeException;
use App\Mapping\EntityMapping;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Lock\LockFactory;

class AccessTokenManager
{
	public function __construct(
		private string        $partnerProjectUrl,
		private EntityManager $entityManager,
		private EntityMapping $entityMapping,
		private Client        $client,
		private LockFactory   $lockFactory,
		private int           $maxAccessTokens = 4
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
					RequestOptions::HEADERS => ['Authorization' => 'Bearer ' . $project->getAccessToken()],
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
		$counter = 0;
		while (!$lock->acquire()) {
			if ($counter >= 15) {
				throw new \Exception('Lease token is locked for 15 seconds!');
			}
			$counter++;
			sleep(1);
		}
		try {
			/** @var AccessToken[] $tokens */
			$tokens = $this->entityManager->getRepository(AccessToken::class)
				->createQueryBuilder('at')
				->where('at.project = :project')
				->andWhere('at.expiresIn > :expiresIn')
				->andWhere('at.invalid = 0')
				->setParameter('project', $project)
				->setParameter('expiresIn', new \DateTimeImmutable())
				->getQuery()->getResult();

			foreach ($tokens as $token) {
				if (!$token->isLeased()) {
					$token->setLeased(true);
					$this->entityManager->flush();
					return $token;
				}
			}

			if (count($tokens) >= $this->maxAccessTokens) {
				throw new \RuntimeException();
			}
			$accessToken = $this->createNewToken($project);
			$accessToken->setLeased(true);
			$this->entityManager->persist($accessToken);
			$this->entityManager->flush();
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
			$dtoAccessToken->access_token,
			(new \DateTimeImmutable())->modify('+ ' . $dtoAccessToken->expires_in . ' sec')
		);
	}

	public function markAsInvalid(AccessToken $accessToken): void
	{
		$accessToken->setInvalid(true);
		$accessToken->setLeased(false);
		$this->entityManager->flush();
	}

	public function returnToken(AccessToken $accessToken): void
	{
		$accessToken->setLeased(false);
		$this->entityManager->flush();
	}
}
