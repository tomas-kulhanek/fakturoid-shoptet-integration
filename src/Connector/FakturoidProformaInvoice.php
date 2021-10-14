<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceItem;
use App\Log\ActionLog;

class FakturoidProformaInvoice extends FakturoidConnector
{
	public function markAsPaid(ProformaInvoice $invoice, \DateTimeImmutable $payAt): \stdClass
	{
		return $this->getAccountingFactory()
			->createClientFromSetting($invoice->getProject()->getSettings())
			->fireInvoice($invoice->getAccountingId(), 'pay_proforma', [
				'paid_at' => $payAt->format('Y-m-d'),
			])->getBody();
	}

	public function createNew(ProformaInvoice $invoice): \stdClass
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'proforma' => true,
			'partial_proforma' => false,
			'subject_id' => $invoice->getOrder()->getCustomer()->getAccountingId(),
			//'subject_custom_id' => 'eh?', //todo
			'correction' => false, //sem v pripade ze jiz byla nahozena todo
			//'correction_id'=> viz vyse todo
			'order_number' => $invoice->getOrder()->getCode(),
			'due' => '15', // z faktury? todo
			'payment_method' => $invoice->getBillingMethod(),
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

		$projectSettings = $invoice->getProject()->getSettings();
		if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof ProformaInvoiceDeliveryAddress) {
			$invoiceData['note'] = $this->getTranslator()->translate('messages.accounting.deliveryAddress') .
				PHP_EOL .
				$this->getAddressFormatter()->format($invoice->getDeliveryAddress(), false);
		}

		/** @var ProformaInvoiceItem $item */
		foreach ($invoice->getOnlyProductItems() as $item) {
			$invoiceData['lines'][] = $this->getLine($item);
		}
		/** @var ProformaInvoiceItem $item */
		foreach ($invoice->getOnlyBillingAndShippingItems() as $item) {
			$invoiceData['lines'][] = $this->getLine($item);
		}

		bdump($invoiceData);
		$this->actionLog->log($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_PROFORMA, $invoice->getId());
		return $this->getAccountingFactory()
			->createClientFromSetting($invoice->getProject()->getSettings())
			->createInvoice($invoiceData)->getBody();
	}

	/**
	 * @param ProformaInvoiceItem $item
	 * @return array<string, float|int|string|null>
	 */
	private function getLine(ProformaInvoiceItem $item): array
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
