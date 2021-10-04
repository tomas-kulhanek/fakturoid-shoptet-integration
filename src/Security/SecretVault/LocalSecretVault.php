<?php

declare(strict_types=1);

namespace App\Security\SecretVault;

use ParagonIE\Halite\Asymmetric\Crypto;
use ParagonIE\Halite\Asymmetric\EncryptionPublicKey;
use ParagonIE\Halite\Asymmetric\EncryptionSecretKey;
use ParagonIE\Halite\HiddenString;
use ParagonIE\Halite\KeyFactory;

final class LocalSecretVault implements ISecretVault
{
	private EncryptionSecretKey $privateKey;

	private EncryptionPublicKey $publicKey;

	public function __construct(string $privateKey, string $publicKey)
	{
		$this->privateKey = KeyFactory::importEncryptionSecretKey(
			new HiddenString($privateKey)
		);
		$this->publicKey = KeyFactory::importEncryptionPublicKey(
			new HiddenString($publicKey)
		);
	}

	public function encrypt(string $password): string
	{
		return Crypto::seal(new HiddenString($password), $this->publicKey);
	}

	public function decrypt(string $crypt): string
	{
		return Crypto::unseal($crypt, $this->privateKey)->getString();
	}
}
