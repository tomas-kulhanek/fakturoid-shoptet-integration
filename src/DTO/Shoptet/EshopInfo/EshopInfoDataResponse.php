<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\EshopInfo;

use JMS\Serializer\Annotation as Serializer;

use Symfony\Component\Validator\Constraints as Assert;

class EshopInfoDataResponse
{
	#[Assert\NotBlank]
	#[Assert\Type(type: EshopInfoResponse::class)]
	public EshopInfoResponse $data;

	/** @var EshopInfoErrorResponse[]|null */
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: 'array<int, EshopInfoErrorResponse>')]
	#[Serializer\Type(name: 'array<App\DTO\Shoptet\Webhooks\EshopInfoErrorResponse>')]
	public ?array $errors = null;

	public function hasErrors(): bool
	{
		return $this->errors !== null && count($this->errors) > 0;
	}
}
