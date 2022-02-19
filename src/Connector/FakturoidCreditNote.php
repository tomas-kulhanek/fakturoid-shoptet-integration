<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\CreditNote;
use App\Database\Entity\Shoptet\CreditNoteDeliveryAddress;
use App\Database\Entity\Shoptet\CreditNoteItem;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Project;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Log\ActionLog;
use App\Mapping\BillingMethodMapper;
use Fakturoid\Exception;
use Ramsey\Uuid\UuidInterface;

//todo!!
class FakturoidCreditNote extends FakturoidConnector
{
	public function getByGuid(Project $project, UuidInterface $guid): \stdClass
	{
		return $this->getAccountingFactory()
			->createClientFromSetting($project->getSettings())
			->getInvoice([
				'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $guid->toString()),
			])->getBody()[0];
	}

	public function cancel(CreditNote $invoice): void
	{
		try {
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
	 * @param CreditNote $invoice
	 * @return \stdClass
	 * @throws EmptyLines
	 * @throws FakturoidException
	 */
	public function createNew(CreditNote $invoice): \stdClass
	{
		$invoiceData = $this->getCreditNoteBaseData($invoice);

		bdump($invoiceData);
		try {
			$invoice->setAccountingError(false);
			$invoice->setAccountingLastError(null);
			$data = $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->createInvoice($invoiceData)->getBody();

			$this->actionLog->logCreditNote($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_CREDIT_NOTE, $invoice, serialize($invoiceData) . PHP_EOL . serialize($data));

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);

			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($invoiceData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanize();
			$invoice->setAccountingError(true);
			$invoice->setAccountingLastError($parsedException->humanize());
			$this->actionLog->logCreditNote($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_CREDIT_NOTE, $invoice, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	/**
	 * @param CreditNote $invoice
	 * @return \stdClass
	 * @throws EmptyLines
	 * @throws FakturoidException
	 */
	public function update(CreditNote $invoice): \stdClass
	{
		$invoiceData = $this->getCreditNoteBaseData($invoice);
		$invoiceData['id'] = $invoice->getAccountingId();

		bdump($invoiceData);
		try {
			$invoice->setAccountingError(false);
			$invoice->setAccountingLastError(null);
			$data = $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->updateInvoice($invoice->getAccountingId(), $invoiceData)->getBody();
			$this->actionLog->logCreditNote($invoice->getProject(), ActionLog::ACCOUNTING_UPDATE_CREDIT_NOTE, $invoice, serialize($invoiceData));

			return $data;
		} catch (Exception $exception) {
			$invoice->setAccountingError(true);
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($invoiceData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanize();
			$invoice->setAccountingLastError($parsedException->humanize());
			$this->actionLog->logCreditNote($invoice->getProject(), ActionLog::ACCOUNTING_UPDATE_CREDIT_NOTE, $invoice, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	/**
	 * @param CreditNote $invoice
	 * @return array<string, mixed>
	 * @throws EmptyLines
	 */
	protected function getCreditNoteBaseData(CreditNote $invoice): array
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'number' => $invoice->getShoptetCode(),
			'number_format_id' => $invoice->getProject()->getSettings()->getAccountingCreditNoteNumberLineId(),
			'proforma' => false,
			'partial_proforma' => false,
			'correction' => true,
			'note' => 'Dobropis pro doklad - ' . $invoice->getInvoiceCode(),
			'transferred_tax_liability' => $this->isReverseCharge($invoice),
			'oss' => $this->detectOssMode($invoice),
			'eu_electronic_service' => false,
			'variable_symbol' => $invoice->getVarSymbol(),
			'subject_id' => $invoice->getCustomer()->getAccountingId(),
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			'tags' => ['shoptet', $invoice->getProject()->getEshopHost()],
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'from_total_with_vat',
			'round_total' => $invoice->getCurrency()->isAccountingRoundTotal(),
			'lines' => [

			],
		];

		if ($invoice->getInvoice() instanceof Invoice && $invoice->getInvoice()->getAccountingId() !== null) {
			$invoiceData['correction_id'] = $invoice->getInvoice()->getAccountingId(); //todo
		}

		if (strlen((string)$invoice->getBillingAddress()->getFullName()) > 0) {
			$invoiceData['client_name'] = $invoice->getBillingAddress()->getFullName();
		}
		if ((($invoice->getCompanyId() !== null && $invoice->getCompanyId() !== '') || ($invoice->getVatId() !== null && $invoice->getVatId() !== '')) && strlen((string)$invoice->getBillingAddress()->getCompany()) > 0) {
			$invoiceData['client_name'] = $invoice->getBillingAddress()->getCompany();
		}
		if (strlen((string)$invoice->getBillingAddress()->getStreet()) > 0) {
			$invoiceData['client_street'] = $invoice->getBillingAddress()->getStreet();
		}
		if (strlen((string)$invoice->getBillingAddress()->getCity()) > 0) {
			$invoiceData['client_city'] = $invoice->getBillingAddress()->getCity();
		}
		if (strlen((string)$invoice->getBillingAddress()->getZip()) > 0) {
			$invoiceData['client_zip'] = $invoice->getBillingAddress()->getZip();
		}
		if (strlen((string)$invoice->getBillingAddress()->getCountryCode()) > 0) {
			$invoiceData['client_country'] = $invoice->getBillingAddress()->getCountryCode();
		}
		if (strlen((string)$invoice->getCompanyId()) > 0) {
			$invoiceData['client_registration_no'] = $invoice->getCompanyId();
		}
		if (strlen((string)$invoice->getVatId()) > 0 && strtolower($invoice->getBillingAddress()->getCountryCode()) !== 'sk') {
			$invoiceData['client_vat_no'] = $invoice->getVatId();
		}
		if (strlen((string)$invoice->getVatId()) > 0 && strtolower($invoice->getBillingAddress()->getCountryCode()) === 'sk') {
			$invoiceData['client_local_vat_no'] = $invoice->getVatId();
		}

		if ($invoice->getCurrency()->getBankAccount() instanceof BankAccount) {
			$invoiceData['bank_account_id'] = $invoice->getCurrency()->getBankAccount()->getAccountingId();
		}

		if ($invoice->getExchangeRate() !== null && $invoice->getExchangeRate() > 0.0) {
			$invoiceData['exchange_rate'] = $invoice->getExchangeRate();
		}
		$language = null;
		if ($invoice->getBillingAddress()->getCountryCode() !== null) {
			$language = strtolower(
				$invoice->getBillingAddress()->getCountryCode()
			);
		}
		if ($invoice->getProject()->getSettings()->getAccountingLanguage() !== null) {
			$language = strtolower(
				$invoice->getProject()->getSettings()->getAccountingLanguage()
			);
		}
		if (in_array($language, self::ALLOWED_LANGUAGES, true)) {
			$invoiceData['language'] = $language;
		}
		$projectSettings = $invoice->getProject()->getSettings();
		if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof CreditNoteDeliveryAddress) {
			$invoiceData['note'] = $this->getTranslator()->translate('messages.accounting.deliveryAddress') .
				PHP_EOL .
				$this->getAddressFormatter()->format($invoice->getDeliveryAddress(), false);
		}

		if ($invoice->getEshopDocumentRemark() !== null) {
			$note = $invoice->getEshopDocumentRemark();
			if ($projectSettings->isPropagateDeliveryAddress() && $invoice->getDeliveryAddress() instanceof CreditNoteDeliveryAddress) {
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

		/** @var CreditNoteItem $item */
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

	public function getLineName(DocumentItem $invoiceItem): string
	{
		if ($invoiceItem->getVariantName() !== null && trim($invoiceItem->getVariantName()) !== '') {
			return sprintf('%s %s', $invoiceItem->getName(), $invoiceItem->getVariantName());
		}
		if ($invoiceItem->getAdditionalField() !== null && trim($invoiceItem->getAdditionalField()) !== '') {
			return sprintf('%s %s', $invoiceItem->getName(), $invoiceItem->getAdditionalField());
		}
		return $invoiceItem->getName();
	}

	/**
	 * @param CreditNoteItem $item
	 * @return array<string, float|int|string|null|bool>
	 */
	private function getLine(CreditNoteItem $item): array
	{
		if ($item->getDeletedAt() instanceof \DateTimeImmutable && $item->getAccountingId() !== null) {
			$data = [
				'_destroy' => true,
				'id' => $item->getAccountingId(),
			];
			$item->setAccountingId(null);
			return $data;
		}
		if ($item->getDeletedAt() instanceof \DateTimeImmutable) {
			return [];
		}
		if ($item->getUnitWithVat() === 0.0) {
			if ($item->getAccountingId() !== null) {
				$data = [
					'_destroy' => true,
					'id' => $item->getAccountingId(),
				];
				$item->setAccountingId(null);
				return $data;
			}
			return [];
		}

		$lineData = [
			'name' => $this->getLineName($item),
			'quantity' => $item->getAmount() * -1,
			'unit_name' => $item->getAmountUnit(),
			'unit_price' => $item->getUnitWithVat(),
			'vat_rate' => $item->getVatRate(),
		];
		if ($item->getAccountingId() !== null) {
			$lineData['id'] = $item->getAccountingId();
		}

		return $lineData;
	}
}
