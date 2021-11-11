<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Log\ActionLog;
use App\Mapping\BillingMethodMapper;
use Fakturoid\Exception;
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

	public function cancel(Invoice $invoice): void
	{
		try {
			if ($invoice->getProformaInvoice() instanceof ProformaInvoice) {
				$this->getAccountingFactory()
					->createClientFromSetting($invoice->getProject()->getSettings())
					->fireInvoice($invoice->getAccountingId(), 'remove_payment');
			} else {
				$this->getAccountingFactory()
					->createClientFromSetting($invoice->getProject()->getSettings())
					->fireInvoice($invoice->getAccountingId(), 'cancel');
			}
			$this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->deleteInvoice($invoice->getAccountingId());
		} catch (Exception $exception) {
			if ($exception->getCode() !== 404) {
				throw  $exception;
			}
		}
	}

	/**
	 * @param Invoice $invoice
	 * @return \stdClass
	 * @throws EmptyLines
	 * @throws FakturoidException
	 */
	public function createNew(Invoice $invoice): \stdClass
	{
		$invoiceData = $this->getInvoiceBaseData($invoice);

		bdump($invoiceData);
		try {
			$data = $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->createInvoice($invoiceData)->getBody();

			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_INVOICE, $invoice);
			$invoice->setAccountingError(false);

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);

			$message = null;
			if (property_exists($parsedException->getErrors(), 'number')) {
				$message = join(' ', $parsedException->getErrors()->number);
			}
			$invoice->setAccountingError(true);
			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_INVOICE, $invoice, $message, $exception->getCode());
			throw  $parsedException;
		}
	}

	/**
	 * @param Invoice $invoice
	 * @return \stdClass
	 * @throws EmptyLines
	 * @throws FakturoidException
	 */
	public function update(Invoice $invoice): \stdClass
	{
		$invoiceData = $this->getInvoiceBaseData($invoice);
		$invoiceData['id'] = $invoice->getAccountingId();

		bdump($invoiceData);
		try {
			$data = $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->updateInvoice($invoice->getAccountingId(), $invoiceData)->getBody();
			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_UPDATE_INVOICE, $invoice);
			$invoice->setAccountingError(false);
			return $data;
		} catch (Exception $exception) {
			$invoice->setAccountingError(true);
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (property_exists($parsedException->getErrors(), 'number')) {
				$message = join(' ', $parsedException->getErrors()->number);
			}
			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_UPDATE_INVOICE, $invoice, $message, $exception->getCode());
			throw  $parsedException;
		}
	}

	/**
	 * @param Invoice $invoice
	 * @return array<string, mixed>
	 * @throws EmptyLines
	 */
	protected function getInvoiceBaseData(Invoice $invoice): array
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'number' => $invoice->getShoptetCode(),
			'proforma' => false,
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

		if ($invoice->getProformaInvoice() instanceof ProformaInvoice && $invoice->getProformaInvoice()->getAccountingId() !== null) {
			$invoiceData['related_id'] = $invoice->getProformaInvoice()->getAccountingId();
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

		$projectSettings = $invoice->getProject()->getSettings();
		if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof InvoiceDeliveryAddress) {
			$invoiceData['note'] = $this->getTranslator()->translate('messages.accounting.deliveryAddress') .
				PHP_EOL .
				$this->getAddressFormatter()->format($invoice->getDeliveryAddress(), false);
		}

		if ($invoice->getEshopDocumentRemark() !== null) {
			$note = $invoice->getEshopDocumentRemark();
			if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof InvoiceDeliveryAddress) {
				$note = $invoiceData['note'] . PHP_EOL . PHP_EOL . $invoice->getEshopDocumentRemark();
			}
			$invoiceData['note'] = $note;
		}

		if ($invoice->getTaxDate() instanceof \DateTimeImmutable) {
			$invoiceData['taxable_fulfillment_due'] = $invoice->getTaxDate()->format('Y-m-d');
		}
		if ($invoice->getIssueDate() instanceof \DateTimeImmutable) {
			$invoiceData['issued_on'] = $invoice->getIssueDate()->format('Y-m-d'); //datum vystaveni
		}
		if ($invoice->getDueDate() instanceof \DateTimeImmutable && $invoice->getIssueDate() instanceof \DateTimeImmutable) {//splatnost ve dnech
			$diff = $invoice->getDueDate()->diff($invoice->getIssueDate());
			$invoiceData['due'] = $diff->days;
		}

		/** @var InvoiceItem $item */
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

	/**
	 * @param InvoiceItem $item
	 * @return array<string, float|int|string|null|bool>
	 */
	private function getLine(InvoiceItem $item): array
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
