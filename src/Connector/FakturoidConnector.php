<?php

declare(strict_types=1);


namespace App\Connector;

use App\Api\FakturoidFactory;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Formatter\AddressFormatter;
use App\Log\ActionLog;
use Fakturoid\Response;
use Nette\Localization\Translator;

abstract class FakturoidConnector
{
	protected const ALLOWED_LANGUAGES = ['cz', 'sk', 'en', 'de', 'fr', 'it', 'es', 'ru', 'hu', 'pl', 'ro'];

	public function __construct(
		private Translator       $translator,
		private AddressFormatter $addressFormatter,
		private FakturoidFactory $accountingFactory,
		protected ActionLog      $actionLog,
		private string           $prefix = 'ev/'
	) {
	}

	public function getInvoiceData(int $invoiceId, ProjectSetting $projectSetting): Response
	{
		return $this->getAccountingFactory()->createClientFromSetting($projectSetting)->getInvoice($invoiceId);
	}

	protected function getInstancePrefix(): string
	{
		return $this->prefix;
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

	public function getInvoice(ProjectSetting $projectSetting, Invoice $document): \stdClass
	{
		$this->actionLog->logInvoice($document->getProject(), ActionLog::ACCOUNTING_INVOICE_DETAIL, $document);
		return $this->getAccountingFactory()->createClientFromSetting($projectSetting)->getInvoice($document->getAccountingId())
			->getBody();
	}

	public function getProformaInvoice(ProjectSetting $projectSetting, ProformaInvoice $document): \stdClass
	{
		$this->actionLog->logProformaInvoice($document->getProject(), ActionLog::ACCOUNTING_PROFORMA_DETAIL, $document);
		return $this->getAccountingFactory()->createClientFromSetting($projectSetting)->getInvoice($document->getAccountingId())
			->getBody();
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
