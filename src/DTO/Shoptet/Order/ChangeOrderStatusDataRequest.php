<?php declare(strict_types=1);


namespace App\DTO\Shoptet\Order;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeOrderStatusDataRequest
{
	#[Assert\NotBlank]
	#[Assert\Type(type: ChangeOrderStatusRequest::class)]
	public ChangeOrderStatusRequest $data;
}
