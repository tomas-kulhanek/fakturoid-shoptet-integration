<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceItem;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Log\ActionLog;
use App\Mapping\BillingMethodMapper;
use Fakturoid\Exception;

class FakturoidProformaInvoice extends FakturoidConnector
{
	public function cancel(ProformaInvoice $proformaInvoice): void
	{
		try {
			if ($proformaInvoice->getInvoice() instanceof Invoice && !$proformaInvoice->getInvoice()->isDeleted()) {
				throw new \Exception('TODO existuje k tomu faktura!');
			}
			$this->getAccountingFactory()
				->createClientFromSetting($proformaInvoice->getProject()->getSettings())
				->deleteInvoice($proformaInvoice->getAccountingId());
		} catch (Exception $exception) {
			if ($exception->getCode() !== 404) {
				throw  $exception;
			}
		}
	}

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
		$invoiceData = $this->getInvoiceBaseData($invoice);
		bdump($invoiceData);
		$this->actionLog->logProformaInvoice($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_PROFORMA, $invoice);
		try {
			return $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->createInvoice($invoiceData)->getBody();
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);

			if ($exception->getCode() !== 422 || !property_exists($parsedException->getErrors(), 'number')) {
				throw $parsedException;
			}
			$this->actionLog->logProformaInvoice($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_PROFORMA, $invoice, join(' ', $parsedException->getErrors()->number));
			throw  $parsedException;
		}
	}

	/**
	 * @param ProformaInvoice $invoice
	 * @return array<string, mixed>
	 * @throws EmptyLines
	 */
	protected function getInvoiceBaseData(ProformaInvoice $invoice): array
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'number' => $invoice->getShoptetCode(),
			'proforma' => true,
			'partial_proforma' => false,
			'note' => null,
			'variable_symbol' => $invoice->getVarSymbol(),
			'subject_id' => $invoice->getCustomer()->getAccountingId(),
			'correction' => false,
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			'tags' => ['shoptet', $invoice->getProject()->getEshopHost()],
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'without_vat',
			'round_total' => false,
			'lines' => [

			],
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
		if ($invoice->getOrder() instanceof Order) {
			$invoiceData['order_number'] = $invoice->getOrder()->getCode();
			if ($invoice->getOrder()->getTaxId() !== null) {
				$language = strtolower(
					substr($invoice->getOrder()->getTaxId(), 0, 2)
				);
				if (in_array($language, self::ALLOWED_LANGUAGES, true)) {
					$invoiceData['language'] = $language;
				}
			}
		}
		if ($invoice->getIssueDate() instanceof \DateTimeImmutable) {
			$invoiceData['issued_on'] = $invoice->getIssueDate()->format('Y-m-d'); //datum vystaveni
		}
		if ($invoice->getDueDate() instanceof \DateTimeImmutable && $invoice->getIssueDate() instanceof \DateTimeImmutable) {//splatnost ve dnech
			$diff = $invoice->getDueDate()->diff($invoice->getIssueDate());
			$invoiceData['due'] = $diff->days;
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
		foreach ($invoice->getItems() as $item) {
			$lineData = $this->getLine($item);
			if (sizeof($lineData) > 0) {
				$invoiceData['lines'][] = $lineData;
			}
		}
		if (sizeof($invoiceData['lines']) < 1) {
			throw new EmptyLines();
		}

		return $invoiceData;
	}

	public function update(ProformaInvoice $invoice): \stdClass
	{
		$invoiceData = $this->getInvoiceBaseData($invoice);
		$invoiceData['id'] = $invoice->getAccountingId();


		bdump($invoiceData);
		$this->actionLog->logProformaInvoice($invoice->getProject(), ActionLog::UPDATE_PROFORMA, $invoice);
		try {
			return $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->updateInvoice($invoice->getAccountingId(), $invoiceData)->getBody();
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);

			if ($exception->getCode() !== 422 || !property_exists($parsedException->getErrors(), 'number')) {
				throw $parsedException;
			}
			$this->actionLog->logProformaInvoice($invoice->getProject(), ActionLog::UPDATE_PROFORMA, $invoice, join(' ', $parsedException->getErrors()->number));
			throw  $parsedException;
		}
	}

	/**
	 * @param ProformaInvoiceItem $item
	 * @return array<string, float|int|string|null|bool>
	 */
	private function getLine(ProformaInvoiceItem $item): array
	{
		if ($item->getDeletedAt() instanceof \DateTimeImmutable && $item->getAccountingId() !== null) {
			$item->setAccountingId(null);
			return [
				'_destroy' => true,
				'id' => $item->getAccountingId(),
			];
		}
		if ($item->getDeletedAt() instanceof \DateTimeImmutable) {
			return [];
		}

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
