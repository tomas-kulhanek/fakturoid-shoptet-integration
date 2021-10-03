<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

interface CreateUserInterface
{
	public function setPassword(string $password): void;

	public function setWebsite(string $website): void;

	public function setEmail(string $email): void;
}
