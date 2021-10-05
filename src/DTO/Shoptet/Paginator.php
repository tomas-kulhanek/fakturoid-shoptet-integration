<?php

declare(strict_types=1);


namespace App\DTO\Shoptet;

use Symfony\Component\Validator\Constraints as Assert;

class Paginator
{
	#[Assert\Type(type: 'integer')]
	public int $totalCount = 0;

	#[Assert\Type(type: 'integer')]
	public int $page = 0;

	#[Assert\Type(type: 'integer')]
	public int $itemsOnPage = 0;

	#[Assert\Type(type: 'integer')]
	public int $itemsPerPage = 0;
}
