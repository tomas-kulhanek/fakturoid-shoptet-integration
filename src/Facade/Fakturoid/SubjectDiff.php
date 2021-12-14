<?php

namespace App\Facade\Fakturoid;

use App\Database\Entity\Shoptet\Document;

class SubjectDiff
{
	public function isDifferent(Document $invoice): bool
	{
		if (
			$invoice->getBillingAddress()->getCompany() !== $invoice->getCustomer()->getBillingAddress()->getCompany()
			|| $invoice->getBillingAddress()->getCountryCode() !== $invoice->getCustomer()->getBillingAddress()->getCountryCode()
			|| $invoice->getBillingAddress()->getStreet() !== $invoice->getCustomer()->getBillingAddress()->getStreet()
			|| $invoice->getBillingAddress()->getCity() !== $invoice->getCustomer()->getBillingAddress()->getCity()
			|| $invoice->getBillingAddress()->getFullName() !== $invoice->getCustomer()->getBillingAddress()->getFullName()
			|| $invoice->getVatId() !== $invoice->getCustomer()->getVatId()
			|| $invoice->getCompanyId() !== $invoice->getCustomer()->getCompanyId()
		) {
			$invoiceBillingData = [];
			$invoiceBillingData['client_name'] = $invoice->getBillingAddress()->getFullName();
			if (($invoice->getCompanyId() !== null && $invoice->getCompanyId() !== '') || ($invoice->getVatId() !== null && $invoice->getVatId() !== '')) {
				$invoiceBillingData['client_name'] = $invoice->getBillingAddress()->getCompany();
			}
			$invoiceBillingData['client_street'] = $invoice->getBillingAddress()->getStreet();
			$invoiceBillingData['client_city'] = $invoice->getBillingAddress()->getCity();
			$invoiceBillingData['client_zip'] = $invoice->getBillingAddress()->getZip();
			$invoiceBillingData['client_country'] = $invoice->getBillingAddress()->getCountryCode();
			$invoiceBillingData['client_registration_no'] = $invoice->getCompanyId();
			$invoiceBillingData['client_vat_no'] = $invoice->getVatId();

			return count(array_filter($invoiceBillingData)) > 0 && strlen((string)$invoiceBillingData['client_name']) > 0;
		}

		return false;
	}
}
