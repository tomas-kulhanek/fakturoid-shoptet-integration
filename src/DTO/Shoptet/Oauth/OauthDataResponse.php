<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Oauth;

use Symfony\Component\Validator\Constraints as Assert;

class OauthDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: 'boolean')]
	public bool $success;

	#[Assert\NotBlank]
	#[Assert\Type(type: OauthResponse::class)]
	public OauthResponse $data;
}
