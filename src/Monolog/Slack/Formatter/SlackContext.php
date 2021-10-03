<?php

declare(strict_types=1);

namespace App\Monolog\Slack\Formatter;

use Nette\Utils\Arrays;

final class SlackContext
{
	/** @var mixed[] */
	private array $data = [];

	/** @var SlackContextField[] */
	private array $fields = [];

	/** @var SlackContextAttachment[] */
	private array $attachments = [];

	/**
	 * @param mixed[] $config
	 * @param mixed[] $context
	 */
	public function __construct(
		private array $config,
		private array $context
	) {
	}

	/**
	 * @param mixed $default
	 * @return mixed
	 */
	public function getConfig(string $key, $default = null)
	{
		return func_num_args() > 1
			? Arrays::get($this->config, explode('.', $key), $default)
			: Arrays::get($this->config, explode('.', $key));
	}

	public function setChannel(string $channel): void
	{
		$this->data['channel'] = $channel;
	}

	public function setUsername(string $username): void
	{
		$this->data['username'] = $username;
	}

	public function setIconEmoji(string $icon): void
	{
		$this->data['icon_emoji'] = sprintf(':%s:', trim($icon, ':'));
	}

	public function setIconUrl(string $icon): void
	{
		$this->data['icon_url'] = $icon;
	}

	public function setText(string $text): void
	{
		$this->data['text'] = $text;
	}

	public function setColor(string $color): void
	{
		$this->data['color'] = $color;
	}

	public function setMarkdown(bool $markdown = true): void
	{
		$this->data['mrkdwn'] = $markdown;
	}

	public function createField(): SlackContextField
	{
		return $this->fields[] = new SlackContextField();
	}

	public function createAttachment(): SlackContextAttachment
	{
		return $this->attachments[] = new SlackContextAttachment();
	}

	public function getExceptionPath(): string
	{
		return $this->context['tracy_url'] ?? '';
	}

	public function getExceptionFileName(): string
	{
		return $this->context['tracy_filename'] ?? 'Exception';
	}

	/**
	 * @return mixed[]
	 */
	public function toArray(): array
	{
		$data = $this->data;

		if (count($this->fields) > 0) {
			$data['fields'] = [];

			foreach ($this->fields as $attachment) {
				$data['fields'][] = $attachment->toArray();
			}
		}

		if (count($this->attachments) > 0) {
			$data['attachments'] = [];

			foreach ($this->attachments as $attachment) {
				$data['attachments'][] = $attachment->toArray();
			}
		}

		return $data;
	}
}
