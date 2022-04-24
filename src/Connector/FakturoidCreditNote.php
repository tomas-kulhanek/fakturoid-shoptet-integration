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
use Nette\Utils\Strings;
use Ramsey\Uuid\UuidInterface;

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

	public function markAsPaid(CreditNote $invoice, \DateTimeImmutable $payAt): void
	{
		$this->getAccountingFactory()
			->createClientFromSetting($invoice->getProject()->getSettings())
			->fireInvoice($invoice->getAccountingId(), 'pay', [
				'paid_at' => $payAt->format('Y-m-d'),
			])->getBody();
	}

	/**
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
	 * @return array<string, mixed>
	 * @throws EmptyLines
	 */
	protected function getCreditNoteBaseData(CreditNote $invoice): array
	{
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'number' => $invoice->getShoptetCode(),
			'number_format_id' => $invoice->getProject()->getSettings()->getAccountingCreditNoteNumberLine()->getAccountingId(),
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
			'tags' => explode(',', $invoice->getProject()->getSettings()->getAccountingCreditNoteTags()),
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'from_total_with_vat',
			'round_total' => $invoice->getCurrency()->isAccountingRoundTotal(),
			'lines' => [

			],
		];

		if ($invoice->getInvoice() instanceof Invoice && $invoice->getInvoice()->getAccountingId() !== null) {
			$invoiceData['correction_id'] = $invoice->getInvoice()->getAccountingId();
		}

		if ($invoice->getAccountingNumberLineId() !== null) {
			$invoiceData['number_format_id'] = $invoice->getAccountingNumberLineId();
		}

		if (Strings::length((string)$invoice->getBillingAddress()->getFullName()) > 0) {
			$invoiceData['client_name'] = $invoice->getBillingAddress()->getFullName();
		}
		if ((($invoice->getCompanyId() !== null && $invoice->getCompanyId() !== '') || ($invoice->getVatId() !== null && $invoice->getVatId() !== '')) && Strings::length((string)$invoice->getBillingAddress()->getCompany()) > 0) {
			$invoiceData['client_name'] = $invoice->getBillingAddress()->getCompany();
		}
		if (Strings::length((string)$invoice->getBillingAddress()->getStreet()) > 0) {
			$invoiceData['client_street'] = $invoice->getBillingAddress()->getStreet();
		}
		if (Strings::length((string)$invoice->getBillingAddress()->getCity()) > 0) {
			$invoiceData['client_city'] = $invoice->getBillingAddress()->getCity();
		}
		if (Strings::length((string)$invoice->getBillingAddress()->getZip()) > 0) {
			$invoiceData['client_zip'] = $invoice->getBillingAddress()->getZip();
		}
		if (Strings::length((string)$invoice->getBillingAddress()->getCountryCode()) > 0) {
			$invoiceData['client_country'] = $invoice->getBillingAddress()->getCountryCode();
		}
		if (Strings::length((string)$invoice->getCompanyId()) > 0) {
			$invoiceData['client_registration_no'] = $invoice->getCompanyId();
		}
		if (Strings::length((string)$invoice->getVatId()) > 0) {
			$invoiceData['client_vat_no'] = $invoice->getVatId();
		}
		if (Strings::length((string)$invoice->getVatId()) > 0 && Strings::lower($invoice->getBillingAddress()->getCountryCode()) === 'sk') {
			$invoiceData['client_local_vat_no'] = Strings::substring($invoice->getVatId(), 2);
		}

		if ($invoice->getCurrency()->getBankAccount() instanceof BankAccount) {
			$invoiceData['bank_account_id'] = $invoice->getCurrency()->getBankAccount()->getAccountingId();
		}

		if ($invoice->getExchangeRate() !== null && $invoice->getExchangeRate() > 0.0) {
			$invoiceData['exchange_rate'] = $invoice->getExchangeRate();
		}
		$language = null;
		if ($invoice->getBillingAddress()->getCountryCode() !== null) {
			$language = Strings::lower(
				$invoice->getBillingAddress()->getCountryCode()
			);
		}
		if ($invoice->getProject()->getSettings()->getAccountingLanguage() !== null) {
			$language = Strings::lower(
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

	public function getLineAmount(DocumentItem $documentItem): float
	{
		return abs($documentItem->getAmount());
	}

	public function getLineUnitPrice(DocumentItem $documentItem): float
	{
		if ($documentItem->getAmount() < 0) {
			return abs($documentItem->getUnitWithVat()) * -1;
		}

		return parent::getLineUnitPrice($documentItem);
	}
}
