<?php declare(strict_types=1);


namespace App\DTO\Shoptet\Oauth;



use Symfony\Component\Validator\Constraints as Assert;
class OauthResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: OauthUserResponse::class)]
	public OauthUserResponse $user;
	#[Assert\NotBlank]
	#[Assert\Type(type: OauthProjectResponse::class)]
	public OauthProjectResponse $project;

}
