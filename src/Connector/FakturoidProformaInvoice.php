<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceItem;
use App\Log\ActionLog;
use App\Mapping\BillingMethodMapper;

class FakturoidProformaInvoice extends FakturoidConnector
{
	public function markAsPaid(ProformaInvoice $invoice, \DateTimeImmutable $payAt): void
	{
		$this->getAccountingFactory()
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
			'correction' => false, //sem v pripade ze jiz byla nahozena todo
			'order_number' => $invoice->getOrder()->getCode(),
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			'tags' => ['shoptet', $invoice->getProject()->getEshopHost()],
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'without_vat', //todo radeji zkontrolovat
			'round_total' => false, //todo asi konfiguracni?
			'lines' => [],
		];
		if ($invoice->getCurrency()->getBankAccount() instanceof BankAccount) {
			$invoiceData['bank_account_id'] = $invoice->getCurrency()->getBankAccount()->getAccountingId();
		}

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

		if ($invoice->getEshopDocumentRemark() !== null) {
			$note = $invoice->getEshopDocumentRemark();
			if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof ProformaInvoiceDeliveryAddress) {
				$note = $invoiceData['note'] . PHP_EOL . PHP_EOL . $invoice->getEshopDocumentRemark();
			}
			$invoiceData['note'] = $note;
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

	public function update(ProformaInvoice $invoice): \stdClass
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'proforma' => true,
			'partial_proforma' => false,
			'subject_id' => $invoice->getOrder()->getCustomer()->getAccountingId(),
			'correction' => false,
			'order_number' => $invoice->getOrder()->getCode(),
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			'tags' => ['shoptet', $invoice->getProject()->getEshopHost()],
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'without_vat',
			'round_total' => false,
			'lines' => [

			],
		];

		if ($invoice->getCurrency()->getBankAccount() instanceof BankAccount) {
			$invoiceData['bank_account_id'] = $invoice->getCurrency()->getBankAccount()->getAccountingId();
		}

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
		if (strlen((string) $invoice->getBillingAddress()->getFullName()) > 0) {
			$invoiceData['client_name'] = $invoice->getBillingAddress()->getFullName();
		}
		if ((($invoice->getCompanyId() !== null && $invoice->getCompanyId() !== '') || ($invoice->getVatId() !== null && $invoice->getVatId() !== '')) && strlen((string) $invoice->getBillingAddress()->getCompany()) > 0) {
			$invoiceData['client_name'] = $invoice->getBillingAddress()->getCompany();
		}
		if (strlen((string) $invoice->getBillingAddress()->getStreet()) > 0) {
			$invoiceData['client_street'] = $invoice->getBillingAddress()->getStreet();
		}
		if (strlen((string) $invoice->getBillingAddress()->getCity()) > 0) {
			$invoiceData['client_city'] = $invoice->getBillingAddress()->getCity();
		}
		if (strlen((string) $invoice->getBillingAddress()->getZip()) > 0) {
			$invoiceData['client_zip'] = $invoice->getBillingAddress()->getZip();
		}
		if (strlen((string) $invoice->getBillingAddress()->getCountryCode()) > 0) {
			$invoiceData['client_country'] = $invoice->getBillingAddress()->getCountryCode();
		}
		if (strlen((string) $invoice->getCompanyId()) > 0) {
			$invoiceData['client_registration_no'] = $invoice->getCompanyId();
		}
		if (strlen((string) $invoice->getVatId()) > 0) {
			$invoiceData['client_vat_no'] = $invoice->getVatId();
		}

		$projectSettings = $invoice->getProject()->getSettings();
		if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof ProformaInvoiceDeliveryAddress) {
			$invoiceData['note'] = $this->getTranslator()->translate('messages.accounting.deliveryAddress') .
				PHP_EOL .
				$this->getAddressFormatter()->format($invoice->getDeliveryAddress(), false);
		}

		if ($invoice->getEshopDocumentRemark() !== null) {
			$note = $invoice->getEshopDocumentRemark();
			if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof ProformaInvoiceDeliveryAddress) {
				$note = $invoiceData['note'] . PHP_EOL . PHP_EOL . $invoice->getEshopDocumentRemark();
			}
			$invoiceData['note'] = $note;
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
			->updateInvoice($invoice->getAccountingId(), $invoiceData)->getBody();
	}

	/**
	 * @param ProformaInvoiceItem $item
	 * @return array<string, float|int|string|null>
	 */
	private function getLine(ProformaInvoiceItem $item): array
	{
		$lineData = [
			'name' => $item->getName(),
			'quantity' => $item->getAmount(),
			'unit_name' => $item->getAmountUnit(),
			'unit_price' => $item->getUnitWithoutVat(),
			'vat_rate' => $item->getVatRate(),
		];
		if ($item->getAccountingId() !== null) {
			$lineData['id'] = $item->getAccountingId();
		}
		return $lineData;
	}
}
