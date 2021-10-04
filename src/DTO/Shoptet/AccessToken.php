<?php declare(strict_types=1);


namespace App\DTO\Shoptet;


use Symfony\Component\Validator\Constraints as Assert;

class AccessToken
{

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $access_token = null;

	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'integer')]
	public ?int $expires_in = null;
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $token_type = null;
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'string')]
	public ?string $scope = null;

	public function getExpiresInMinutes(): int
	{
		if ($this->expires_in === null) {
			return 0;
		}
		return $this->expires_in / 60;
	}
}
