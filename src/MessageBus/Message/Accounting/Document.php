<?php

namespace App\MessageBus\Message\Accounting;

abstract class Document
{
	public function __construct(
		private int $eshopId,
		private int $documentId
	) {
	}

	public function getEshopId(): int
	{
		return $this->eshopId;
	}

	public function getDocumentId(): int
	{
		return $this->documentId;
	}
}
