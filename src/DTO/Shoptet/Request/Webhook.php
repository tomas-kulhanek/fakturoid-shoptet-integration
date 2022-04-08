<?php

declare(strict_types=1);


namespace App\DTO\Shoptet\Request;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class Webhook
{
	public const TYPE_CREDIT_NOTE_CREATE = 'creditNote:create';
	public const TYPE_CREDIT_NOTE_DELETE = 'creditNote:delete';
	public const TYPE_CREDIT_NOTE_UPDATE = 'creditNote:update';
	public const TYPE_CUSTOMER_CREATE = 'customer:create';
	public const TYPE_CUSTOMER_IMPORT = 'customer:import';
	public const TYPE_DELIVERY_NOTE_CREATE = 'deliveryNote:create';
	public const TYPE_DELIVERY_NOTE_DELETE = 'deliveryNote:delete';
	public const TYPE_DELIVERY_NOTE_UPDATE = 'deliveryNote:update';
	public const TYPE_ESHOP_MANDATORY_FIELDS = 'eshop:mandatoryFields';
	public const TYPE_INVOICE_CREATE = 'invoice:create';
	public const TYPE_INVOICE_DELETE = 'invoice:delete';
	public const TYPE_INVOICE_UPDATE = 'invoice:update';
	public const TYPE_JOB_FINISHED = 'job:finished';
	public const TYPE_MAILING_LIST_EMAIL_CREATE = 'mailingListEmail:create';
	public const TYPE_MAILING_LIST_EMAIL_DELETE = 'mailingListEmail:delete';
	public const TYPE_ORDER_CREATE = 'order:create';
	public const TYPE_ORDER_DELETE = 'order:delete';
	public const TYPE_ORDER_UPDATE = 'order:update';
	public const TYPE_PROFORMA_INVOICE_CREATE = 'proformaInvoice:create';
	public const TYPE_PROFORMA_INVOICE_DELETE = 'proformaInvoice:delete';
	public const TYPE_PROFORMA_INVOICE_UPDATE = 'proformaInvoice:update';
	public const TYPE_SHIPPING_REQUEST_CANCELLED = 'shippingRequest:cancelled';
	public const TYPE_SHIPPING_REQUEST_CONFIRMED = 'shippingRequest:confirmed';
	public const TYPE_STOCK_MOVEMENT = 'stock:movement';
	public const TYPE_ADDON_SUSPEND = 'addon:suspend';
	public const TYPE_ADDON_APPROVE = 'addon:approve';
	public const TYPE_ADDON_UNINSTALL = 'addon:uninstall';

	public const ALL_TYPES = [
		self::TYPE_CREDIT_NOTE_CREATE,
		self::TYPE_CREDIT_NOTE_DELETE,
		self::TYPE_CREDIT_NOTE_UPDATE,
		self::TYPE_CUSTOMER_CREATE,
		self::TYPE_CUSTOMER_IMPORT,
		self::TYPE_DELIVERY_NOTE_CREATE,
		self::TYPE_DELIVERY_NOTE_DELETE,
		self::TYPE_DELIVERY_NOTE_UPDATE,
		self::TYPE_ESHOP_MANDATORY_FIELDS,
		self::TYPE_INVOICE_CREATE,
		self::TYPE_INVOICE_DELETE,
		self::TYPE_INVOICE_UPDATE,
		self::TYPE_JOB_FINISHED,
		self::TYPE_MAILING_LIST_EMAIL_CREATE,
		self::TYPE_MAILING_LIST_EMAIL_DELETE,
		self::TYPE_ORDER_CREATE,
		self::TYPE_ORDER_DELETE,
		self::TYPE_ORDER_UPDATE,
		self::TYPE_PROFORMA_INVOICE_CREATE,
		self::TYPE_PROFORMA_INVOICE_DELETE,
		self::TYPE_PROFORMA_INVOICE_UPDATE,
		self::TYPE_SHIPPING_REQUEST_CANCELLED,
		self::TYPE_SHIPPING_REQUEST_CONFIRMED,
		self::TYPE_STOCK_MOVEMENT,
		self::TYPE_ADDON_SUSPEND,
		self::TYPE_ADDON_APPROVE,
		self::TYPE_ADDON_UNINSTALL,
	];

	#[Assert\NotBlank()]
	#[Assert\Type(type: 'integer')]
	public int $eshopId;
	#[Assert\NotBlank()]
	#[Assert\Choice(choices: self::ALL_TYPES)]
	public string $event;
	#[Assert\NotBlank(allowNull: true)]
	#[Assert\Type(type: DateTimeImmutable::class)]
	public \DateTimeImmutable $eventCreated;
	#[Assert\Type(type: 'string')]
	public string $eventInstance = '';

	/**
	 * @return string[]
	 */
	public function getAddonSystemEventTypes(): array
	{
		return [
			self::TYPE_ADDON_SUSPEND,
			self::TYPE_ADDON_APPROVE,
			self::TYPE_ADDON_UNINSTALL,
		];
	}
}
