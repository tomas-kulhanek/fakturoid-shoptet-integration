<?php

declare(strict_types=1);


namespace App;

final class Application
{
	public const DESTINATION_FRONT_HOMEPAGE = ':Front:Home:';
	public const DESTINATION_APP_HOMEPAGE = ':App:Home:';
	public const DESTINATION_SIGN_IN = ':Front:Sign:in';
	public const DESTINATION_AFTER_SIGN_IN = self::DESTINATION_APP_HOMEPAGE;
	public const DESTINATION_AFTER_SIGN_OUT = self::DESTINATION_SIGN_IN;

	public const DESTINATION_FORCE_CHANGE_PASSWORD = ':Front:Sign:setPassword';

	public const DESTINATION_OAUTH_CONFIRM = 'Front:Sign:oauthConfirm';
	public const DESTINATION_WEBHOOK = 'Api:Shoptet:webhook';
	public const DESTINATION_INSTALLATION_CONFIRM = 'Api:Shoptet:installation';
}
