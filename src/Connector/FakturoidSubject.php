<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Document;
use App\Exception\FakturoidException;
use App\Log\ActionLog;
use Fakturoid\Exception;

class FakturoidSubject extends FakturoidConnector
{
	public function createNew(Customer $customer, Document $document): \stdClass
	{
		$this->prepareCustomerData($customer, $document);
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

		try {
			$data = $this->getAccountingFactory()
				->createClientFromSetting($customer->getProject()->getSettings())
				->createSubject($customerData)->getBody();

			$this->actionLog->logCustomer($customer->getProject(), ActionLog::ACCOUNTING_CREATE_SUBJECT, $customer);

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']);
			}

			$this->actionLog->logCustomer($customer->getProject(), ActionLog::ACCOUNTING_CREATE_SUBJECT, $customer, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	private function prepareCustomerData(Customer $customer, Document $document): void
	{
		if ($customer->getBillingAddress()->getCompany() === null) {
			$customer->getBillingAddress()->setCompany($document->getBillingAddress()->getCompany());
		}
		if ($customer->getBillingAddress()->getFullName() === null) {
			$customer->getBillingAddress()->setFullName($document->getBillingAddress()->getFullName());
		}
		if ($customer->getBillingAddress()->getStreet() === null) {
			$customer->getBillingAddress()->setStreet($document->getBillingAddress()->getStreet());
		}
		if ($customer->getBillingAddress()->getCity() === null) {
			$customer->getBillingAddress()->setCity($document->getBillingAddress()->getCity());
		}
		if ($customer->getBillingAddress()->getZip() === null) {
			$customer->getBillingAddress()->setZip($document->getBillingAddress()->getZip());
		}
		if ($customer->getBillingAddress()->getCountryCode() === null) {
			$customer->getBillingAddress()->setCountryCode($document->getBillingAddress()->getCountryCode());
		}
		if ($customer->getCompanyId() === null) {
			$customer->setCompanyId($document->getCompanyId());
		}
		if ($customer->getVatId() === null) {
			$customer->setVatId($document->getVatId());
		}
		if ($customer->getEmail() === null) {
			$customer->setEmail($document->getEmail());
		}
		if ($customer->getPhone() === null) {
			$customer->setPhone($document->getPhone());
		}
	}
}
