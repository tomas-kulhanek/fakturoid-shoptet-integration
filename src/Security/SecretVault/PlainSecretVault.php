<?php

declare(strict_types=1);

namespace App\Security\SecretVault;

final class PlainSecretVault implements ISecretVault
{
	public function __construct(string $privateKey, string $publicKey)
	{
	}

	public function encrypt(string $password): string
	{
		return $password;
	}

	public function decrypt(string $crypt): string
	{
		return $crypt;
	}
}
