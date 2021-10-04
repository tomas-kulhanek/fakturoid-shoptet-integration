<?php declare(strict_types=1);


namespace App\DTO\Shoptet\Oauth;


use Symfony\Component\Validator\Constraints as Assert;

class OauthUserResponse
{
	#[Assert\NotBlank(allowNull: false)]
	#[Assert\Type(type: 'string')]
	public string $email;
	#[Assert\NotBlank(allowNull: false)]
	#[Assert\Type(type: 'string')]
	public string $name;

}
