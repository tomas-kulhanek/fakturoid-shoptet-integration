<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\InvoiceDeliveryAddress;
use App\Database\Entity\Shoptet\InvoiceItem;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\Entity\Shoptet\Project;
use App\Exception\Accounting\EmptyLines;
use App\Exception\FakturoidException;
use App\Log\ActionLog;
use App\Mapping\BillingMethodMapper;
use Fakturoid\Exception;
use Nette\Utils\Strings;
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

	public function sendMail(Invoice $invoice): void
	{
		$this->getAccountingFactory()
			->createClientFromSetting($invoice->getProject()->getSettings())
			->createMessage($invoice->getAccountingId(), [
				'email' => $invoice->getEmail()
			]);
	}

	public function markAsPaid(Invoice $invoice, \DateTimeImmutable $payAt): void
	{
		$this->getAccountingFactory()
			->createClientFromSetting($invoice->getProject()->getSettings())
			->fireInvoice($invoice->getAccountingId(), 'pay', [
				'paid_at' => $payAt->format('Y-m-d'),
			])->getBody();
	}

	public function cancel(Invoice $invoice): void
	{
		try {
			if ($invoice->getProformaInvoice() instanceof ProformaInvoice) {
				$this->getAccountingFactory()
					->createClientFromSetting($invoice->getProject()->getSettings())
					->fireInvoice($invoice->getAccountingId(), 'remove_payment');
			} else {
				try {
					$this->getAccountingFactory()
						->createClientFromSetting($invoice->getProject()->getSettings())
						->fireInvoice($invoice->getAccountingId(), 'cancel');
				} catch (Exception) {
				}
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
	 * @throws EmptyLines
	 * @throws FakturoidException
	 */
	public function createNew(Invoice $invoice): \stdClass
	{
		$invoiceData = $this->getInvoiceBaseData($invoice);

		bdump($invoiceData);
		try {
			$invoice->setAccountingError(false);
			$invoice->setAccountingLastError(null);
			$data = $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->createInvoice($invoiceData)->getBody();

			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_INVOICE, $invoice, serialize($invoiceData));

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);

			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($invoiceData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanizeCreateNewInvoice();
			$invoice->setAccountingError(true);
			$invoice->setAccountingLastError($parsedException->humanizeCreateNewInvoice());
			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_CREATE_INVOICE, $invoice, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	/**
	 * @throws EmptyLines
	 * @throws FakturoidException
	 */
	public function update(Invoice $invoice): \stdClass
	{
		$invoiceData = $this->getInvoiceBaseData($invoice);
		$invoiceData['id'] = $invoice->getAccountingId();

		bdump($invoiceData);
		try {
			$invoice->setAccountingError(false);
			$invoice->setAccountingLastError(null);
			$data = $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->updateInvoice($invoice->getAccountingId(), $invoiceData)->getBody();
			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_UPDATE_INVOICE, $invoice, serialize($invoiceData));

			return $data;
		} catch (Exception $exception) {
			$invoice->setAccountingError(true);
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($invoiceData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanizeEditInvoice();
			$invoice->setAccountingLastError($parsedException->humanizeEditInvoice());
			$this->actionLog->logInvoice($invoice->getProject(), ActionLog::ACCOUNTING_UPDATE_INVOICE, $invoice, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	/**
	 * @return array<string, mixed>
	 * @throws EmptyLines
	 */
	protected function getInvoiceBaseData(Invoice $invoice): array
	{
		$projectSettings = $invoice->getProject()->getSettings();
		$invoiceData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $invoice->getGuid()->toString()),
			'number' => $invoice->getShoptetCode(),
			'proforma' => false,
			'partial_proforma' => false,
			'note' => null,
			'transferred_tax_liability' => $this->isReverseCharge($invoice),
			'oss' => $this->detectOssMode($invoice),
			'eu_electronic_service' => false,
			'variable_symbol' => $invoice->getVarSymbol(),
			'subject_id' => $invoice->getCustomer()->getAccountingId(),
			'correction' => false,
			'payment_method' => $invoice->getBillingMethod() ?? BillingMethodMapper::BILLING_METHOD_BANK,
			'tags' => explode(',', $invoice->getProject()->getSettings()->getAccountingInvoiceTags()),
			'currency' => $invoice->getCurrencyCode(),
			'vat_price_mode' => 'from_total_with_vat',
			'round_total' => $invoice->getCurrency()->isAccountingRoundTotal(),
			'lines' => [

			],
		];

		if ($invoice->getProject()->getSettings()->getAccountingNumberLine() !== null) {
			$invoiceData['number_format_id'] = $invoice->getProject()->getSettings()->getAccountingNumberLine()->getAccountingId();// ID ciselne rady - /numbering/339510/edit
		}

		if ($invoice->getAccountingNumberLineId() !== null) {
			$invoiceData['number_format_id'] = $invoice->getAccountingNumberLineId();
		}

		if ($invoice->getProformaInvoice() instanceof ProformaInvoice && $invoice->getProformaInvoice()->getAccountingId() !== null) {
			$invoiceData['related_id'] = $invoice->getProformaInvoice()->getAccountingId();
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
			if (Strings::lower($invoice->getCurrencyCode()) !== Strings::lower($projectSettings->getAccountingDefaultCurrency()) && ($invoice->getExchangeRate() < 1.0 || $invoice->getExchangeRate() > 1.0)) {
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

		//if ($invoice->getEet() !== NULL && $invoice->getEet()->isActive()) {
		//	$invoiceData['eet_records'] = [];
		//	$invoiceData['eet_records'][] = [
		//		'id' => $invoice->getEet()->getAccountingId(),
		//		'vat_no' => $invoice->getEet()->getVatId(),
		//		'cash_register' => $invoice->getEet()->getCashDeskId(),
		//		'vat_base0' => $invoice->getEet()->getNonTaxableBase(),
		//		'vat_base1' => $invoice->getEet()->getVatBase1(),
		//		'vat1' => $invoice->getEet()->getVat1(),
		//		'vat_base2' => $invoice->getEet()->getVatBase2(),
		//		'vat2' => $invoice->getEet()->getVat2(),
		//		'vat_base3' => $invoice->getEet()->getVatBase3(),
		//		'vat3' => $invoice->getEet()->getVat3(),
		//		'fik' => $invoice->getEet()->getFik(),
		//		'bkp' => $invoice->getEet()->getBkp(),
		//		'pkp' => $invoice->getEet()->getPkp(),
		//		'playground' => $invoice->getEet()->getEetMod() !== 'production',
		//		'external' => TRUE,
		//		'total' => $invoice->getEet()->getTotalRevenue(),
		//		'last_uuid' => $invoice->getEet()->getUuid(),
		//		'number' => $invoice->getShoptetCode(),
		//		'invoice_id' => $invoice->getAccountingId(),
//
		//		'store' => '',
		//		'paid_at' => '',
		//		'status' => 'nevim',
		//		'fik_received_at' => '',
		//		'attempts' => '',
		//		'last_attempt_at' => '',
		//	];
		//}


		$lineIds = [];
		/** @var InvoiceItem $item */
		foreach ($invoice->getItems() as $item) {
			$lineData = $this->getLine($item);
			if (sizeof($lineData) > 0) {
				$lineIds[] = $item->getAccountingId();
				$invoiceData['lines'][] = $lineData;
			}
		}
		$lineIds = array_filter($lineIds);
		if ($invoice->getProject()->getId() === 22 && $invoice->getAccountingId() !== null) {
			$invoiceFakturoid = $this->getAccountingFactory()
				->createClientFromSetting($invoice->getProject()->getSettings())
				->getInvoice($invoice->getAccountingId())->getBody();
			foreach ($invoiceFakturoid->lines as $line) {
				if (!in_array($line->id, $lineIds, true)) {
					$invoiceData['lines'][] = ['id' => $line->id, '_destroy' => true, ];
				}
			}
		}
		if (sizeof($invoiceData['lines']) < 1) {
			throw new EmptyLines();
		}

		return $invoiceData;
	}
}
