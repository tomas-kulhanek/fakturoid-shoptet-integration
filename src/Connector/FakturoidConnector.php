<?php

declare(strict_types=1);


namespace App\Connector;

use App\Api\FakturoidFactory;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Document;
use App\Formatter\AddressFormatter;
use Nette\Localization\Translator;

abstract class FakturoidConnector
{
	private const HTML_URL = 'https://app.fakturoid.cz/%s/invoices/%d';
	private const PUBLIC_HTML_URL = 'https://app.fakturoid.cz/%s/p/%s/%s';
	private const URL = 'https://app.fakturoid.cz/api/v2/accounts/%s/invoices/%d.json';
	private const PDF_URL = 'https://app.fakturoid.cz/api/v2/accounts/%s/invoices/%d/download.pdf';
	private const SUBJECT_URL = 'https://app.fakturoid.cz/api/v2/accounts/%s/subjects/%d.json';
	protected const ALLOWED_LANGUAGES = ['cz', 'sk', 'en', 'de', 'fr', 'it', 'es', 'ru', 'hu', 'pl', 'ro'];

	public function __construct(
		private Translator       $translator,
		private AddressFormatter $addressFormatter,
		private FakturoidFactory $accountingFactory,
		private string           $prefix = 'ev/'
	) {
	}

	protected function getInstancePrefix(): string
	{
		return $this->prefix;
	}


	public function getHtmlUrl(Document $invoice): string
	{
		return sprintf(
			self::HTML_URL,
			$invoice->getProject()->getSettings()->getAccountingAccount(),
			$invoice->getAccountingId()
		);
	}

	public function getPublicHtmlUrl(Document $invoice): string
	{
		return sprintf(
			self::PUBLIC_HTML_URL,
			$invoice->getProject()->getSettings()->getAccountingAccount(),
			$invoice->getAccountingPublicToken(),
			$invoice->getAccountingNumber()
		);
	}

	public function getUrl(Document $invoice): string
	{
		return sprintf(
			self::URL,
			$invoice->getProject()->getSettings()->getAccountingAccount(),
			$invoice->getAccountingId()
		);
	}

	public function getPDFUrl(Document $invoice): string
	{
		return sprintf(
			self::PDF_URL,
			$invoice->getProject()->getSettings()->getAccountingAccount(),
			$invoice->getAccountingId()
		);
	}

	public function getSubjectUrl(Document $invoice): string
	{
		return sprintf(
			self::SUBJECT_URL,
			$invoice->getProject()->getSettings()->getAccountingAccount(),
			$invoice->getAccountingSubjectId()
		);
	}

	public function getProformaInvoices(ProjectSetting $projectSetting): \stdClass
	{
		//todo params
		return $this->getAccountingFactory()->createClientFromSetting($projectSetting)->getProformaInvoices()
			->getBody();
	}

	public function getInvoices(ProjectSetting $projectSetting): \stdClass
	{
		//todo params
		return $this->getAccountingFactory()->createClientFromSetting($projectSetting)->getInvoices()
			->getBody();
	}

	public function getInvoice(ProjectSetting $projectSetting, string $invoiceId): \stdClass
	{
		return $this->getAccountingFactory()->createClientFromSetting($projectSetting)->getInvoice($invoiceId)
			->getBody();
	}

	public function getProformaInvoice(ProjectSetting $projectSetting, string $invoiceId): \stdClass
	{
		return $this->getInvoice($projectSetting, $invoiceId);
	}

	public function getTranslator(): Translator
	{
		return $this->translator;
	}

	public function getAddressFormatter(): AddressFormatter
	{
		return $this->addressFormatter;
	}

	public function getAccountingFactory(): FakturoidFactory
	{
		return $this->accountingFactory;
	}
}
