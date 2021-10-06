<?php

declare(strict_types=1);


namespace App\Api;

use Contributte\Guzzlette\ClientFactory;
use Fakturoid\Response;

class FakturoidRequester
{
	private \GuzzleHttp\Client $client;

	/**
	 * @param ClientFactory $clientFactory
	 * @param array<string, string|int> $defaultHeaders
	 */
	public function __construct(
		ClientFactory $clientFactory,
		array         $defaultHeaders
	) {
		$this->client = $clientFactory->createClient(['headers' => $defaultHeaders]);
	}


	/**
	 * @param array<string|string> $options
	 * @return Response
	 * @throws \GuzzleHttp\Exception\GuzzleException
	 */
	public function run(array $options): Response
	{
		$auth = explode(':', $options['userpwd']);
		$response = $this->client->request(
			method: $options['method'],
			uri: $options['url'],
			options: [
				'params' => $options['params'],
				'body' => $options['body'],
				'headers' => $options['headers'],
				'auth' => $auth,
			]
		);
		$headers = [];
		foreach ($response->getHeaders() as $headerName => $headerValues) {
			$headers[$headerName] = implode('; ', $headerValues);
		}
		return new Response(['http_code' => $response->getStatusCode(), 'headers' => $headers], $response->getBody()->getContents());
	}
}
