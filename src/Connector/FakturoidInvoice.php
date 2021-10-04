<?php

declare(strict_types=1);


namespace App\Connector;

use App\Api\FakturoidFactory;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Formatter\AddressFormatter;
use Nette\Localization\Translator;

class FakturoidInvoice
{
	private const HTML_URL = 'https://app.fakturoid.cz/%s/invoices/%d';
	private const PUBLIC_HTML_URL = 'https://app.fakturoid.cz/%s/p/%s/%s';
	private const URL = 'https://app.fakturoid.cz/api/v2/accounts/%s/invoices/%d.json';
	private const PDF_URL = 'https://app.fakturoid.cz/api/v2/accounts/%s/invoices/%d/download.pdf';
	private const SUBJECT_URL = 'https://app.fakturoid.cz/api/v2/accounts/%s/subjects/%d.json';

	public function __construct(
		private Translator $translator,
		private AddressFormatter $addressFormatter,
		private FakturoidFactory $fakturoidFactory
	) {
	}

	public function getHtmlUrl(Invoice $invoice): string
	{
		return sprintf(
			self::HTML_URL,
			$invoice->getProject()->getSettings()->getFakturoidAccount(),
			$invoice->getFakturoidId()
		);
	}

	public function getPublicHtmlUrl(Invoice $invoice): string
	{
		return sprintf(
			self::PUBLIC_HTML_URL,
			$invoice->getProject()->getSettings()->getFakturoidAccount(),
			$invoice->getFakturoidPublicToken(),
			$invoice->getFakturoidNumber()
		);
	}

	public function getUrl(Invoice $invoice): string
	{
		return sprintf(
			self::URL,
			$invoice->getProject()->getSettings()->getFakturoidAccount(),
			$invoice->getFakturoidId()
		);
	}

	public function getPDFUrl(Invoice $invoice): string
	{
		return sprintf(
			self::PDF_URL,
			$invoice->getProject()->getSettings()->getFakturoidAccount(),
			$invoice->getFakturoidId()
		);
	}

	public function getSubjectUrl(Invoice $invoice): string
	{
		return sprintf(
			self::SUBJECT_URL,
			$invoice->getProject()->getSettings()->getFakturoidAccount(),
			$invoice->getFakturoidSubjectId()
		);
	}

	public function createNew(Invoice $invoice): \stdClass
	{
		$invoiceData = [
			'custom_id' => sprintf('%s/%s', $invoice->getOrder()->getCode(), $invoice->getId()),
			'proforma' => false,
			'partial_proforma' => false,
			'subject_id' => '12763418', //todo
			//'subject_custom_id' => 'eh?', //todo
			'correction' => false, //sem v pripade ze jiz byla nahozena todo
			//'correction_id'=> viz vyse todo
			'order_number' => $invoice->getOrder()->getCode(),
			'due' => '15', // z faktury? todo

			'footer_note' => '', //todo
			'tags' => ['shoptet'],
			'bank_account_id' => '', //todo, bylo by to super!
			'currency' => $invoice->getCurrencyCode(),
			'exchange_rate' => $invoice->getExchangeRate(),
			//'language' => $invoice->getOrder()->getLanguage(),
			'transferred_tax_liability' => '', //todo co sem?
			'supply_code' => '', //todo co sem?
			'vat_price_mode' => 'without_vat', //todo radeji zkontrolovat
			'round_total' => false, //todo asi konfiguracni?
			'lines' => [

			],

		];
		if ($invoice->getTaxDate() instanceof \DateTimeImmutable) {
			$invoiceData['taxable_fulfillment_due'] = $invoice->getTaxDate()->format('Y-m-d');
		}
		$projectSettings = $invoice->getProject()->getSettings();
		if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof InvoiceDeliveryAddress) {
			$invoiceData['note'] = $this->translator->translate('messages.fakturoid.deliveryAddress') . PHP_EOL . $this->addressFormatter->format($invoice->getDeliveryAddress(), false);
		}

		/** @var InvoiceItem $item */
		foreach ($invoice->getOnlyProductItems() as $item) {
			$invoiceData['lines'][] = $this->getLine($item);
		}
		/** @var InvoiceItem $item */
		foreach ($invoice->getOnlyBillingAndShippingItems()->filter(fn (DocumentItem $item) => (float) $item->getUnitWithoutVat() > 0.0) as $item) {
			$invoiceData['lines'][] = $this->getLine($item);
		}

		return $this->fakturoidFactory
			->createClient($invoice->getProject()->getSettings())
			->createInvoice($invoiceData)->getBody();
	}

	/**
	 * @param InvoiceItem $item
	 * @return array<string,string|null>
	 */
	private function getLine(InvoiceItem $item): array
	{
		return [
			'name' => $item->getName(),
			'quantity' => $item->getAmount(),
			'unit_name' => $item->getAmountUnit(),
			'unit_price' => $item->getUnitWithoutVat(),
			'vat_rate' => $item->getVatRate(),
		];
	}
}
