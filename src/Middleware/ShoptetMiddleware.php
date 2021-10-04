<?php

declare(strict_types=1);


namespace App\Middleware;

use App\Application;
use App\Utils\Validator\InitiatorValidatorInterface;
use Contributte\Middlewares\IMiddleware;
use Contributte\Psr7\Psr7ServerRequest;
use Nette\Application\LinkGenerator;
use Nette\Http\IResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ShoptetMiddleware implements IMiddleware
{
	public function __construct(
		private InitiatorValidatorInterface $initiatorValidator,
		private LinkGenerator $linkGenerator
	) {
	}

	/**
	 * @param Psr7ServerRequest|ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param callable $next
	 * @return ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		/** @var Psr7ServerRequest $request */
		$allowedUrls = [
			$this->linkGenerator->link(Application::DESTINATION_INSTALLATION_CONFIRM),
			$this->linkGenerator->link(Application::DESTINATION_WEBHOOK),
		];
		if (!in_array($request->getHttpRequest()->getUrl()->getAbsoluteUrl(), $allowedUrls, true)) {
			return $next($request, $response);
		}
		bdump($request->getHttpRequest()->getUrl());
		if ($this->initiatorValidator->validateIpAddress($request->getHttpRequest())) {
			return $next($request, $response);
		}
		return $response->withStatus(IResponse::S401_UNAUTHORIZED);
	}
}
