<?php

declare(strict_types=1);


namespace App\Api;

use Fakturoid\Response;

class FakturoidRequester
{
	public function __construct(
		private \GuzzleHttp\ClientInterface $client
	) {
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
