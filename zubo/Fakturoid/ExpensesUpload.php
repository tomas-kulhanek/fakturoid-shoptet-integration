<?php

namespace TomasKulhanek\Fakturoid;

use Fakturoid\Client;

class ExpensesUpload extends FakturoidData
{

	public function __construct(private Client $client)
	{
	}

	public function import(\XMLWorker $xml, bool $onlyWithoutVat, array $bankAccountMap, int $zapoctemId): void
	{
		$proformas = $xml->parseValue2Array('/export/receivedInvoices/receivedInvoice[string-length(@fakturoidId)=0]/@id');
		if (empty($proformas)) {
			return;
		}
		$i = 0;
		foreach ($proformas[\XMLWorker::INDEX_ATTRIBUTE]['id'] as $invoiceId) {
			$proforma = $xml->parseValue2Array('/export/receivedInvoices/receivedInvoice[string-length(@fakturoidId)=0 and @id="' . $invoiceId[\XMLWorker::INDEX_VALUE] . '"]');

			if (isset($proforma[\XMLWorker::INDEX_ATTRIBUTE]['fakturoidError'][\XMLWorker::INDEX_VALUE])) {
				$xml->removeXml($proforma[\XMLWorker::INDEX_XPATH] . '/@fakturoidError');
			}
			$proformaData = $proforma[\XMLWorker::INDEX_ELEMENT]['receivedInvoice'][0];
			if (!empty($proformaData[\XMLWorker::INDEX_ATTRIBUTE]['fakturoidId'][\XMLWorker::INDEX_VALUE])) {
				continue;
			}

			$iban = $proformaData[\XMLWorker::INDEX_ELEMENT]['MyCompanyDocumentAddress'][0][\XMLWorker::INDEX_ELEMENT]['VatIdentificationNumber'][0][\XMLWorker::INDEX_VALUE] ?? NULL;//todo
			if ($onlyWithoutVat && !empty($iban)) {
				continue;
			}
			if (empty($proformaData[\XMLWorker::INDEX_ELEMENT]['AttachmentFileName'][0][\XMLWorker::INDEX_VALUE])) {
				//continue;
			}
			$this->processImport($proformaData, $xml, $onlyWithoutVat, $bankAccountMap, $zapoctemId);
			$i++;
			file_put_contents(__DIR__ . '/../hahaa.xml', $xml->getXml());
		}
	}

	private function processImport(array $proforma, \XMLWorker $xml, bool $onlyWithoutVat, array $bankAccountMap, int $zapoctemId)
	{
		$iban = $proforma[\XMLWorker::INDEX_ELEMENT]['MyCompanyDocumentAddress'][0][\XMLWorker::INDEX_ELEMENT]['VatIdentificationNumber'][0][\XMLWorker::INDEX_VALUE] ?? NULL;//todo
		if ($onlyWithoutVat && !empty($iban)) {
			return;
		}
		echo '.';
		if (!empty($proforma[\XMLWorker::INDEX_ATTRIBUTE]['fakturoidId'][\XMLWorker::INDEX_VALUE])) {
			return;
		}

		try {

			$data = $this->getInvoiceData($proforma, $xml, $onlyWithoutVat, $bankAccountMap, $zapoctemId, __DIR__ . '/../attachments/receivedInvoices/%s.pdf', TRUE);

			if (!empty($proforma[\XMLWorker::INDEX_ELEMENT]['Discount'][0][\XMLWorker::INDEX_VALUE])) {
				var_dump('BACHA!!! - ' . $proforma[\XMLWorker::INDEX_ATTRIBUTE]['id'][\XMLWorker::INDEX_VALUE]);
			}
			$response = $this->client->createExpense($data)->getBody();

			if (!isset($proforma[\XMLWorker::INDEX_ATTRIBUTE]['fakturoidId'][\XMLWorker::INDEX_VALUE])) {
				$xml->insertAttribute($proforma[\XMLWorker::INDEX_XPATH], 'fakturoidId', $response->id);
			}
			$xml->setValue($proforma[\XMLWorker::INDEX_XPATH] . '/@fakturoidId', $response->id);

			if (isset($proforma[\XMLWorker::INDEX_ATTRIBUTE]['fakturoidError'][\XMLWorker::INDEX_VALUE])) {
				$xml->setValue($proforma[\XMLWorker::INDEX_XPATH] . '/@fakturoidError', '');
			}
			if (!$this->isSameExpense($proforma, $xml)) {
				unset($data['lines']);
				$this->client->updateExpense($response->id, $data);
			}
			if ($proforma[\XMLWorker::INDEX_ELEMENT]['PaymentStatus'][0][\XMLWorker::INDEX_VALUE] !== '0') {
				$this->client->fireExpense($response->id, 'pay', [
					'paid_on' => $proforma[\XMLWorker::INDEX_ELEMENT]['DateOfPayment'][0][\XMLWorker::INDEX_VALUE],
				]);
			}
		} catch (\Throwable $exception) {
			var_dump($exception->getMessage());
			if (!isset($proforma[\XMLWorker::INDEX_ATTRIBUTE]['fakturoidError'][\XMLWorker::INDEX_VALUE])) {
				$xml->insertAttribute($proforma[\XMLWorker::INDEX_XPATH], 'fakturoidError', $exception->getMessage());
			}
			$xml->setValue($proforma[\XMLWorker::INDEX_XPATH] . '/@fakturoidError', $exception->getMessage());
		}
	}
}
