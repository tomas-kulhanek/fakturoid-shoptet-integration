<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Project;
use App\Log\ActionLog;
use App\Mapping\BillingMethodMapper;
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
			//'due' => '15', // z faktury? todo
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			//'footer_note' => '', //todo
			'tags' => ['shoptet', $invoice->getProject()->getEshopHost()],
			'currency' => $invoice->getCurrencyCode(),
			//'transferred_tax_liability' => '', //todo co sem?
			//'supply_code' => '', //todo co sem?
			'vat_price_mode' => 'without_vat', //todo radeji zkontrolovat
			'round_total' => false, //todo asi konfiguracni?
			'lines' => [],
		];
		$invoiceData['client_name'] = $invoice->getBillingAddress()->getFullName();
		if (($invoice->getCompanyId() !== null && $invoice->getCompanyId() !== '') || ($invoice->getVatId() !== null && $invoice->getVatId() !== '')) {
			$invoiceData['client_name'] = $invoice->getBillingAddress()->getCompany();
		}
		$invoiceData['client_street'] = $invoice->getBillingAddress()->getStreet();
		$invoiceData['client_city'] = $invoice->getBillingAddress()->getCity();
		$invoiceData['client_zip'] = $invoice->getBillingAddress()->getZip();
		$invoiceData['client_country'] = $invoice->getBillingAddress()->getCountryCode();
		$invoiceData['client_registration_no'] = $invoice->getCompanyId();
		$invoiceData['client_vat_no'] = $invoice->getVatId();

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

	public function update(Invoice $invoice): \stdClass
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'proforma' => false,
			'partial_proforma' => false,
			'subject_id' => $invoice->getOrder()->getCustomer()->getAccountingId(),
			'correction' => false,
			'order_number' => $invoice->getOrder()->getCode(),
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			'tags' => ['shoptet', $invoice->getProject()->getEshopHost()],
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'without_vat', //todo radeji zkontrolovat
			'round_total' => false, //todo asi konfiguracni?
			'lines' => [],
		];
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
			->updateInvoice($invoice->getAccountingId(), $invoiceData)->getBody();
	}

	/**
	 * @param InvoiceItem $item
	 * @return array<string, float|int|string|null>
	 */
	private function getLine(InvoiceItem $item): array
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
