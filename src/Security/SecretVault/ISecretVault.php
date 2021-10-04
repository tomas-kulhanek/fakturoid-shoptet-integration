<?php

declare(strict_types=1);

namespace App\Security\SecretVault;

interface ISecretVault
{
	public function encrypt(string $secret): string;

	public function decrypt(string $crypt): string;
}
