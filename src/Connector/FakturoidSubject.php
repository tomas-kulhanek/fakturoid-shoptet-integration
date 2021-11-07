<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Shoptet\Customer;
use App\Log\ActionLog;

class FakturoidSubject extends FakturoidConnector
{
	public function createNew(Customer $customer): \stdClass
	{
		$customerData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $customer->getGuid()->toString()),
			'type' => 'customer',
			'name' => $customer->getBillingAddress()->getCompany() ?? $customer->getBillingAddress()->getFullName(),
			'street' => $customer->getBillingAddress()->getStreet(),
			'city' => $customer->getBillingAddress()->getCity(),
			'zip' => $customer->getBillingAddress()->getZip(),
			'country' => $customer->getBillingAddress()->getCountryCode(),
			'registration_no' => $customer->getCompanyId(),
			'vat_no' => $customer->getVatId(),
			//'local_vat_no' => $customer->getBillingAddress()->, todo toto mi chybi od Shoptetu
			'enabled_reminders' => $customer->getProject()->getSettings()->isAccountingReminder(),
			'full_name' => $customer->getBillingAddress()->getFullName(),
			'email' => $customer->getEmail(),
			'phone' => $customer->getPhone(),
			'private_note' => $customer->getBillingAddress()->getAdditional(),
		];
		bdump(array_filter($customerData));
		$this->actionLog->logCustomer($customer->getProject(), ActionLog::ACCOUNTING_CREATE_SUBJECT, $customer);
		return $this->getAccountingFactory()
			->createClientFromSetting($customer->getProject()->getSettings())
			->createSubject($customerData)->getBody();
	}
}
