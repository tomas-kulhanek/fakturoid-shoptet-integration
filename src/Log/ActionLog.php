<?php

declare(strict_types=1);


namespace App\Log;

use App\Database\Entity\CustomerActionLog;
use App\Database\Entity\InvoiceActionLog;
use App\Database\Entity\OrderInvoiceActionLog;
use App\Database\Entity\ProformaInvoiceActionLog;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;

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
		private EntityManager $entityManager
	) {
	}

	public function logOrder(Project $project, string $type, Order $document, ?string $message = null, ?int $errorCode = null, bool $isError = false, bool $flush = true): void
	{
		$log = new OrderInvoiceActionLog();
		$log->setDocument($document);
		$log->setReferenceCode($document->getShoptetCode());
		$this->log($log, $project, $type, $message, $errorCode, $isError, $flush);
	}

	public function logInvoice(Project $project, string $type, Invoice $document, ?string $message = null, ?int $errorCode = null, bool $isError = false, bool $flush = true): void
	{
		$log = new InvoiceActionLog();
		$log->setDocument($document);
		$log->setReferenceCode($document->getShoptetCode());
		$this->log($log, $project, $type, $message, $errorCode, $isError, $flush);
	}

	public function logCustomer(Project $project, string $type, Customer $document, ?string $message = null, ?int $errorCode = null, bool $isError = false, bool $flush = true): void
	{
		$log = new CustomerActionLog();
		$log->setDocument($document);
		$log->setReferenceCode($document->getShoptetGuid() ?? '');
		$this->log($log, $project, $type, $message, $errorCode, $isError, $flush);
	}

	public function logProformaInvoice(Project $project, string $type, ProformaInvoice $document, ?string $message = null, ?int $errorCode = null, bool $isError = false, bool $flush = true): void
	{
		$log = new ProformaInvoiceActionLog();
		$log->setDocument($document);
		$log->setReferenceCode($document->getShoptetCode());
		$this->log($log, $project, $type, $message, $errorCode, $isError, $flush);
	}

	protected function log(\App\Database\Entity\ActionLog $actionLog, Project $project, string $type, ?string $message, ?int $errorCode = null, bool $isError = false, bool $flush = true): void
	{
		$actionLog->setProject($project);
		$actionLog->setType($type);
		$actionLog->setMessage($message);
		$actionLog->setErrorCode($errorCode);
		$actionLog->setError($isError);
		$this->entityManager->persist($actionLog);
		if ($flush) {
			$this->entityManager->flush();
		}
	}
}
