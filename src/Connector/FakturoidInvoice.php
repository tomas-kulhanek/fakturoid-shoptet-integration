<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Project;
use App\Log\ActionLog;
use Ramsey\Uuid\UuidInterface;

class FakturoidInvoice extends FakturoidConnector
{

	public function getByGuid(Project $project, UuidInterface $guid): \stdClass
	{
		return $this->getAccountingFactory()
			->createClientFromSetting($project->getSettings())
			->getInvoices([
				'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $guid->toString()),
			])->getBody()[0];
	}

	public function createNew(Invoice $invoice): \stdClass
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'proforma' => false,
			'partial_proforma' => false,
			'subject_id' => $invoice->getOrder()->getCustomer()->getAccountingId(),
			//'subject_custom_id' => 'eh?', //todo
			'correction' => false, //sem v pripade ze jiz byla nahozena todo
			//'correction_id'=> viz vyse todo
			'order_number' => $invoice->getOrder()->getCode(),
			'due' => '15', // z faktury? todo

			'footer_note' => '', //todo
			'tags' => ['shoptet', $invoice->getProject()->getEshopHost()],
			'bank_account_id' => '', //todo, bylo by to super!
			'currency' => $invoice->getCurrencyCode(),
			'transferred_tax_liability' => '', //todo co sem?
			'supply_code' => '', //todo co sem?
			'vat_price_mode' => 'without_vat', //todo radeji zkontrolovat
			'round_total' => false, //todo asi konfiguracni?
			'lines' => [

			],
		];
		if ($invoice->getExchangeRate() !== null && $invoice->getExchangeRate() > 0.0) {
			$invoiceData['exchange_rate'] = $invoice->getExchangeRate();
		}
		if ($invoice->getOrder()->getTaxId() !== null) {
			$language = strtolower(
				substr($invoice->getOrder()->getTaxId(), 0, 2)
			);
			if (in_array($language, self::ALLOWED_LANGUAGES, true)) {
				$invoiceData['language'] = $language;
			}
		}
		if ($invoice->getTaxDate() instanceof \DateTimeImmutable) {
			$invoiceData['taxable_fulfillment_due'] = $invoice->getTaxDate()->format('Y-m-d');
		}
		$projectSettings = $invoice->getProject()->getSettings();
		if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof InvoiceDeliveryAddress) {
			$invoiceData['note'] = $this->getTranslator()->translate('messages.accounting.deliveryAddress') .
				PHP_EOL .
				$this->getAddressFormatter()->format($invoice->getDeliveryAddress(), false);
		}

		/** @var InvoiceItem $item */
		foreach ($invoice->getOnlyProductItems() as $item) {
			$invoiceData['lines'][] = $this->getLine($item);
		}
		/** @var InvoiceItem $item */
		foreach ($invoice->getOnlyBillingAndShippingItems() as $item) {
			$invoiceData['lines'][] = $this->getLine($item);
		}

		bdump($invoiceData);
		$this->actionLog->log($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_INVOICE, $invoice->getId());
		return $this->getAccountingFactory()
			->createClientFromSetting($invoice->getProject()->getSettings())
			->createInvoice($invoiceData)->getBody();
	}

	/**
	 * @param InvoiceItem $item
	 * @return array<string, float|int|string|null>
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
