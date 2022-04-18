<?php

declare(strict_types=1);


namespace App\Connector;

use App\Api\FakturoidFactory;
use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\DocumentItem;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\ProformaInvoiceItem;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Formatter\AddressFormatter;
use App\Log\ActionLog;
use App\Mapping\BillingMethodMapper;
use Fakturoid\Exception;
use Nette\Localization\Translator;
use Nette\Utils\Strings;

class FakturoidProformaInvoice extends FakturoidConnector
{
	public function __construct(
		Translator       $translator,
		AddressFormatter $addressFormatter,
		FakturoidFactory $accountingFactory,
		ActionLog        $actionLog,
		string           $prefix = 'ev/'
	) {
		parent::__construct($translator, $addressFormatter, $accountingFactory, $actionLog, $prefix);
	}

	public function cancel(ProformaInvoice $proformaInvoice): void
	{
		try {
			if ($proformaInvoice->getInvoice() instanceof Invoice && !$proformaInvoice->getInvoice()->isDeleted()) {
				throw new \Exception('TODO existuje k tomu faktura!');
			}
			$proformaInvoice->setAccountingError(false);
			$proformaInvoice->setAccountingLastError(null);
			$this->getAccountingFactory()
				->createClientFromSetting($proformaInvoice->getProject()->getSettings())
				->deleteInvoice($proformaInvoice->getAccountingId());
		} catch (Exception $exception) {
			$proformaInvoice->setAccountingError(true);
			if ($exception->getCode() !== 404) {
				throw  $exception;
			}
		}
	}

	public function sendMail(ProformaInvoice $proformaInvoice): void
	{
		$this->getAccountingFactory()
			->createClientFromSetting($proformaInvoice->getProject()->getSettings())
			->createMessage($proformaInvoice->getAccountingId(), [
				'email' => $proformaInvoice->getEmail()
			]);
	}

	public function markAsPaid(ProformaInvoice $proformaInvoice, \DateTimeImmutable $payAt): void
	{
		try {
			$proformaInvoice->setAccountingError(false);
			$proformaInvoice->setAccountingLastError(null);
			$this->getAccountingFactory()
				->createClientFromSetting($proformaInvoice->getProject()->getSettings())
				->fireInvoice($proformaInvoice->getAccountingId(), 'pay_proforma', [
					'paid_at' => $payAt->format('Y-m-d'),
				])->getBody();
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('related_id', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['related_id']) . PHP_EOL;
			}
			$message .= ' - ' . $parsedException->humanize();
			$proformaInvoice->setAccountingError(true);
			$proformaInvoice->setAccountingLastError($parsedException->humanize());
			$this->actionLog->logProformaInvoice($proformaInvoice->getProject(), ActionLog::ACCOUNTING_CREATE_PROFORMA, $proformaInvoice, $message, $exception->getCode(), true);
			throw $parsedException;
		}
	}

	public function createNew(ProformaInvoice $proformaInvoice): \stdClass
	{
		$invoiceData = $this->getInvoiceBaseData($proformaInvoice);
		bdump($invoiceData);
		try {
			$proformaInvoice->setAccountingError(false);
			$proformaInvoice->setAccountingLastError(null);
			$data = $this->getAccountingFactory()
				->createClientFromSetting($proformaInvoice->getProject()->getSettings())
				->createInvoice($invoiceData)->getBody();

			$this->actionLog->logProformaInvoice($proformaInvoice->getProject(), ActionLog::ACCOUNTING_CREATE_PROFORMA, $proformaInvoice, serialize($invoiceData));

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($invoiceData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanize();
			$proformaInvoice->setAccountingError(true);
			$proformaInvoice->setAccountingLastError($parsedException->humanize());
			$this->actionLog->logProformaInvoice($proformaInvoice->getProject(), ActionLog::ACCOUNTING_CREATE_PROFORMA, $proformaInvoice, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	/**
	 * @return array<string, mixed>
	 * @throws EmptyLines
	 */
	protected function getInvoiceBaseData(ProformaInvoice $invoice): array
	{
		$projectSettings = $invoice->getProject()->getSettings();
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'number' => $invoice->getShoptetCode(),
			'proforma' => true,
			'partial_proforma' => false,
			'note' => null,
			'transferred_tax_liability' => $this->isReverseCharge($invoice),
			'oss' => $this->detectOssMode($invoice),
			'variable_symbol' => $invoice->getVarSymbol(),
			'subject_id' => $invoice->getCustomer()->getAccountingId(),
			'correction' => false,
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			'tags' => explode(',', $invoice->getProject()->getSettings()->getAccountingProformaInvoiceTags()),
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'from_total_with_vat',
			'round_total' => $invoice->getCurrency()->isAccountingRoundTotal(),
			'lines' => [

			],
		];

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
			if (Strings::lower($invoice->getCurrencyCode()) !== $projectSettings->getAccountingDefaultCurrency() && ($invoice->getExchangeRate() < 1.0 || $invoice->getExchangeRate() > 1.0)) {
				$invoiceData['exchange_rate'] = $invoice->getExchangeRate();
			}
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
		if ($invoice->getIssueDate() instanceof \DateTimeImmutable) {
			$invoiceData['issued_on'] = $invoice->getIssueDate()->format('Y-m-d'); //datum vystaveni
		}
		if ($invoice->getDueDate() instanceof \DateTimeImmutable && $invoice->getIssueDate() instanceof \DateTimeImmutable) {//splatnost ve dnech
			$diff = $invoice->getDueDate()->diff($invoice->getIssueDate());
			$invoiceData['due'] = $diff->days;
		}

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

	public function update(ProformaInvoice $proformaInvoice): \stdClass
	{
		$invoiceData = $this->getInvoiceBaseData($proformaInvoice);
		$invoiceData['id'] = $proformaInvoice->getAccountingId();


		bdump($invoiceData);
		try {
			$proformaInvoice->setAccountingError(false);
			$proformaInvoice->setAccountingLastError(null);
			$data = $this->getAccountingFactory()
				->createClientFromSetting($proformaInvoice->getProject()->getSettings())
				->updateInvoice($proformaInvoice->getAccountingId(), $invoiceData)->getBody();

			$this->actionLog->logProformaInvoice($proformaInvoice->getProject(), ActionLog::ACCOUNTING_UPDATE_PROFORMA, $proformaInvoice, serialize($invoiceData));

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($invoiceData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanize();
			$proformaInvoice->setAccountingError(true);
			$proformaInvoice->setAccountingLastError($parsedException->humanize());
			$this->actionLog->logProformaInvoice($proformaInvoice->getProject(), ActionLog::ACCOUNTING_UPDATE_PROFORMA, $proformaInvoice, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}
}
