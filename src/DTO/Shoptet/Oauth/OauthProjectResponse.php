<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Oauth;

use Symfony\Component\Validator\Constraints as Assert;

class OauthProjectResponse
{
	#[Assert\NotBlank(allowNull: false)]
	#[Assert\Type(type: 'integer')]
	public int $id;
	#[Assert\NotBlank(allowNull: false)]
	#[Assert\Type(type: 'string')]
	public string $url;
	#[Assert\NotBlank(allowNull: false)]
	#[Assert\Type(type: 'string')]
	public string $name;
}
