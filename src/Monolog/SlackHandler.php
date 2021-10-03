<?php

declare(strict_types=1);


namespace App\Monolog;

use App\Exception\LogicException;
use App\Monolog\Slack\Formatter\IFormatter;
use App\Monolog\Slack\Formatter\SlackContext;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\MissingExtensionException;
use Monolog\Logger;
use Tracy\ILogger;

class SlackHandler extends AbstractProcessingHandler
{
	/** @var IFormatter[] */
	private array $formatters = [];

	/** @var array<string,string> */
	private array $config = [
		'url' => '',
		'channel' => '',
		'username' => 'Tracy',
		'icon_emoji' => ':rocket:',
		'icon_url' => '',
	];

	public function __construct(
		string $url,
		string $channel,
		string $username = 'Tracy',
		string $icon_emoji = ':rocket:',
		string $icon_url = '',
		$level = Logger::CRITICAL,
		bool $bubble = true
	) {
		if (!extension_loaded('curl')) {
			throw new MissingExtensionException('The curl extension is needed to use the SlackWebhookHandler');
		}

		parent::__construct($level, $bubble);
		$this->config = array_merge($this->config, [
			'url' => $url,
			'channel' => $channel,
			'username' => $username,
			'icon_emoji' => $icon_emoji,
			'icon_url' => $icon_url,
		]);
	}

	public function addFormatter(IFormatter $formatter): void
	{
		$this->formatters[] = $formatter;
	}


	/**
	 * {@inheritDoc}
	 */
	protected function write(array $record): void
	{
		if (!$record['context']['exception'] instanceof \Throwable) {
			return;
		}

		$priority = strtolower($record['level_name'] ?: ILogger::ERROR);
		if (!in_array($priority, [ILogger::ERROR, ILogger::EXCEPTION, ILogger::CRITICAL], true)) {
			return;
		}
		$context = new SlackContext($this->config, $record['context']);

		foreach ($this->formatters as $formatter) {
			$context = $formatter->format($context, $record['context']['exception'], $priority);
		}

		$streamContext = [
			'http' => [
				'method' => 'POST',
				'header' => 'Content-type: application/x-www-form-urlencoded',
				'timeout' => 30,
				'content' => http_build_query([
					'payload' => json_encode(array_filter($context->toArray())),
				]),
			],
		];

		@file_get_contents($this->config['url'], false, stream_context_create($streamContext));
	}
}
