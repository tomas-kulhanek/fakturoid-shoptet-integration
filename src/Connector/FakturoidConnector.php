<?php

declare(strict_types=1);


namespace App\Connector;

use App\Api\FakturoidFactory;
use App\Database\Entity\ProjectSetting;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\DocumentItem;
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
		return $this->getAccountingFactory()->createClientFromSetting($projectSetting)->getProformaInvoices()
			->getBody();
	}

	public function getInvoices(ProjectSetting $projectSetting): \stdClass
	{
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

	protected function isOssService(Document $document): bool
	{
		return $document->getEshopTaxMode() === 'OSS' && !$document->getItems()->filter(fn (DocumentItem $documentItem) => $documentItem->getItemType() === 'service')->isEmpty();
	}

	protected function isOssGoods(Document $document): bool
	{
		return $document->getEshopTaxMode() === 'OSS' && !$this->isOssService($document);
	}

	protected function isReverseCharge(Document $document): bool
	{
		return $document->getEshopTaxMode() === 'REVERSE_CHARGE';
	}

	protected function detectOssMode(Document $document): string
	{
		if ($this->isOssService($document)) {
			return 'service';
		}
		if ($this->isOssGoods($document)) {
			return 'goods';
		}
		return 'disabled';
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
	 * @return array<string, float|int|string|null|bool>
	 */
	protected function getLine(DocumentItem $item): array
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

		$lineData = [
			'name' => $this->getLineName($item),
			'quantity' => $this->getLineAmount($item),
			'unit_name' => $item->getAmountUnit(),
			'unit_price' => $this->getLineUnitPrice($item),
			'vat_rate' => $item->getVatRate(),
		];
		if ($item->getAccountingId() !== null) {
			$lineData['id'] = $item->getAccountingId();
		}

		return $lineData;
	}

	public function getLineAmount(DocumentItem $documentItem): float
	{
		return $documentItem->getAmount();
	}

	public function getLineUnitPrice(DocumentItem $documentItem): float
	{
		return $documentItem->getUnitWithVat();
	}
}
