<?php

declare(strict_types=1);


namespace App\Api;

use App\Database\Entity\Shoptet\Project;
use App\Mapping\EntityMapping;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractClient implements ClientInterface
{
	abstract protected function getHttpClient(): \GuzzleHttp\Client;

	abstract public function getClientId(): string;

	abstract protected function getClientSecret(): string;

	abstract protected function getEntityMapping(): EntityMapping;

	abstract protected function sendRequest(string $method, Project $project, string $url, ?string $data = null): ResponseInterface;
}
