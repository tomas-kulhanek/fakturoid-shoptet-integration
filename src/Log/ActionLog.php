<?php

declare(strict_types=1);


namespace App\Log;

use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Security\SecurityUser;

class ActionLog
{
	public const UPDATE_ORDER = 'order.update';
	public const CREATE_ORDER = 'order.create';
	public const UPDATE_INVOICE = 'invoice.update';
	public const CREATE_INVOICE = 'invoice.create';
	public const UPDATE_PROFORMA = 'proforma.update';
	public const CREATE_PROFORMA = 'proforma.create';
	public const UPDATE_CREDIT_NOTE = 'creditNote.update';
	public const CREATE_CREDIT_NOTE = 'creditNote.create';

	public const SHOPTET_ORDER_DETAIL = 'shoptet.order.detail';
	public const SHOPTET_INVOICE_DETAIL = 'shoptet.invoice.detail';
	public const SHOPTET_PROFORMA_DETAIL = 'shoptet.proforma.detail';
	public const SHOPTET_CREDIT_NOTE_DETAIL = 'shoptet.creditNote.detail';
	public const SHOPTET_CUSTOMER_DETAIL = 'shoptet.customer.detail';

	public const ACCOUNTING_INVOICE_DETAIL = 'accounting.invoice.detail';
	public const ACCOUNTING_PROFORMA_DETAIL = 'accounting.proforma.detail';
	public const ACCOUNTING_CREDIT_NOTE_DETAIL = 'accounting.creditNote.detail';
	public const ACCOUNTING_CUSTOMER_DETAIL = 'accounting.customer.detail';

	public const ACCOUNTING_INVOICE_LIST = 'accounting.invoice.list';
	public const ACCOUNTING_PROFORMA_LIST = 'accounting.proforma.list';
	public const ACCOUNTING_CREDIT_NOTE_LIST = 'accounting.creditNote.list';
	public const ACCOUNTING_CUSTOMER_LIST = 'accounting.customer.list';

	public const ACCOUNTING_CREATE_INVOICE = 'accounting.invoice.create';
	public const ACCOUNTING_UPDATE_INVOICE = 'accounting.invoice.update';
	public const ACCOUNTING_CREATE_PROFORMA = 'accounting.proforma.create';
	public const ACCOUNTING_UPDATE_PROFORMA = 'accounting.proforma.update';
	public const ACCOUNTING_CREATE_SUBJECT = 'accounting.subject.create';
	public const ACCOUNTING_UPDATE_SUBJECT = 'accounting.subject.update';


	public const TYPES = [
		self::UPDATE_ORDER,
		self::CREATE_ORDER,
		self::UPDATE_INVOICE,
		self::CREATE_INVOICE,
		self::UPDATE_PROFORMA,
		self::CREATE_PROFORMA,
		self::UPDATE_CREDIT_NOTE,
		self::CREATE_CREDIT_NOTE,

		self::SHOPTET_ORDER_DETAIL,
		self::SHOPTET_INVOICE_DETAIL,
		self::SHOPTET_PROFORMA_DETAIL,
		self::SHOPTET_CREDIT_NOTE_DETAIL,
		self::SHOPTET_CUSTOMER_DETAIL,

		self::ACCOUNTING_INVOICE_DETAIL,
		self::ACCOUNTING_PROFORMA_DETAIL,
		self::ACCOUNTING_CREDIT_NOTE_DETAIL,
		self::ACCOUNTING_CUSTOMER_DETAIL,
		self::ACCOUNTING_INVOICE_LIST,
		self::ACCOUNTING_PROFORMA_LIST,
		self::ACCOUNTING_CREDIT_NOTE_LIST,
		self::ACCOUNTING_CUSTOMER_LIST,

		self::ACCOUNTING_CREATE_INVOICE,
		self::ACCOUNTING_UPDATE_INVOICE,
		self::ACCOUNTING_CREATE_PROFORMA,
		self::ACCOUNTING_UPDATE_PROFORMA,
		self::ACCOUNTING_CREATE_SUBJECT,
		self::ACCOUNTING_UPDATE_SUBJECT,
	];

	public function __construct(
		private EntityManager $entityManager,
		private SecurityUser $user
	) {
	}

	public function log(Project $project, string $type, int|null $referenceId = null, string $userIdentifier = 'CLI', bool $flush = true): void
	{
		if ($this->user->isLoggedIn()) {
			$userIdentifier = $this->user->getIdentity()->getData()['email'];
		}
		$log = new \App\Database\Entity\ActionLog(
			$project,
			$type,
			$userIdentifier,
			$referenceId
		);
		$this->entityManager->persist($log);
		if ($flush) {
			$this->entityManager->flush($log);
		}
	}
}
