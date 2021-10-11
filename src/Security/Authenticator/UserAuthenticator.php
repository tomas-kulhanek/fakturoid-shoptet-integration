<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Exception\Runtime\AuthenticationException;
use Nette\Security;
use Nette\Security\IIdentity;

final class UserAuthenticator implements Security\Authenticator
{
	/**
	 * @param string $username
	 * @param string $password
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(string $username, string $password): IIdentity
	{
		throw new AuthenticationException();
	}
}
