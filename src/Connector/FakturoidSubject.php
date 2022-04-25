<?php

declare(strict_types=1);


namespace App\Connector;

use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\Project;
use App\Exception\FakturoidException;
use App\Log\ActionLog;
use Fakturoid\Exception;
use Nette\Utils\Strings;

class FakturoidSubject extends FakturoidConnector
{
	public function createNew(Customer $customer, Document $document): \stdClass
	{
		$this->prepareCustomerData($customer, $document);
		$customerData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $customer->getGuid()->toString()),
			'type' => 'customer',
			'name' => $customer->getBillingAddress()->getCompany(),
			'street' => $customer->getBillingAddress()->getStreet(),
			'city' => $customer->getBillingAddress()->getCity(),
			'zip' => $customer->getBillingAddress()->getZip(),
			'country' => $customer->getBillingAddress()->getCountryCode(),
			'enabled_reminders' => $customer->getProject()->getSettings()->isAccountingReminder(),
			'full_name' => $customer->getBillingAddress()->getFullName(),
			'email' => $customer->getEmail(),
			'phone' => $customer->getPhone(),
			'private_note' => $customer->getBillingAddress()->getAdditional(),
		];

		if (Strings::length((string)$customer->getCompanyId()) > 0) {
			$customerData['registration_no'] = $customer->getCompanyId();
		}
		if (Strings::length((string)$customer->getVatId()) > 0) {
			$customerData['vat_no'] = $customer->getVatId();
		}
		if (Strings::length((string)$customer->getVatId()) > 0 && Strings::lower($customer->getBillingAddress()->getCountryCode()) === 'sk') {
			$customerData['local_vat_no'] = Strings::substring($customer->getVatId(), 2);
		}


		$companyName = $customer->getBillingAddress()->getCompany();
		if ($companyName === null || trim($companyName) === '') {
			$customerData['name'] = $customer->getBillingAddress()->getFullName();
		}

		try {
			$data = $this->getAccountingFactory()
				->createClientFromSetting($customer->getProject()->getSettings())
				->createSubject($customerData)->getBody();

			$this->actionLog->logCustomer($customer->getProject(), ActionLog::ACCOUNTING_CREATE_SUBJECT, $customer, serialize($customerData));

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($customerData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanizeCreateNewSubject();

			$this->actionLog->logCustomer($customer->getProject(), ActionLog::ACCOUNTING_CREATE_SUBJECT, $customer, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	public function update(Customer $customer, Document $document, string $type = 'customer'): \stdClass
	{
		$this->prepareCustomerData($customer, $document);
		$customerData = [
			'custom_id' => sprintf('%s%s', $this->getInstancePrefix(), $customer->getGuid()->toString()),
			'type' => $type,
			'name' => $customer->getBillingAddress()->getCompany(),
			'street' => $customer->getBillingAddress()->getStreet(),
			'city' => $customer->getBillingAddress()->getCity(),
			'zip' => $customer->getBillingAddress()->getZip(),
			'country' => $customer->getBillingAddress()->getCountryCode(),
			'enabled_reminders' => $customer->getProject()->getSettings()->isAccountingReminder(),
			'full_name' => $customer->getBillingAddress()->getFullName(),
			'email' => $customer->getEmail(),
			'phone' => $customer->getPhone(),
			'private_note' => $customer->getBillingAddress()->getAdditional(),
		];

		if (Strings::length((string)$customer->getCompanyId()) > 0) {
			$customerData['registration_no'] = $customer->getCompanyId();
		}
		if (Strings::length((string)$customer->getVatId()) > 0) {
			$customerData['vat_no'] = $customer->getVatId();
		}
		if (Strings::length((string)$customer->getVatId()) > 0 && Strings::lower($customer->getBillingAddress()->getCountryCode()) === 'sk') {
			$customerData['local_vat_no'] = Strings::substring($customer->getVatId(), 2);
		}


		$companyName = $customer->getBillingAddress()->getCompany();
		if ($companyName === null || trim($companyName) === '') {
			$customerData['name'] = $customer->getBillingAddress()->getFullName();
		}

		try {
			$data = $this->getAccountingFactory()
				->createClientFromSetting($customer->getProject()->getSettings())
				->updateSubject($customer->getAccountingId(), $customerData)->getBody();

			$this->actionLog->logCustomer($customer->getProject(), ActionLog::ACCOUNTING_UPDATE_SUBJECT, $customer, serialize($customerData));

			return $data;
		} catch (Exception $exception) {
			$parsedException = FakturoidException::createFromLibraryExcpetion($exception);
			$message = null;
			if (array_key_exists('number', $parsedException->getErrors())) {
				$message = join(' ', $parsedException->getErrors()['number']) . PHP_EOL;
			}
			$message .= ' - ' . serialize($customerData) . PHP_EOL;
			$message .= ' - ' . $parsedException->humanize();

			$this->actionLog->logCustomer($customer->getProject(), ActionLog::ACCOUNTING_UPDATE_SUBJECT, $customer, $message, $exception->getCode(), true);
			throw  $parsedException;
		}
	}

	/**
	 * @param string $query
	 * @param Project $project
	 * @return \stdClass[]
	 * @throws FakturoidException
	 */
	public function findIdByQuery(string $query, Project $project): array
	{
		try {
			return $this->getAccountingFactory()
				->createClientFromSetting($project->getSettings())
				->searchSubjects(['query' => $query])->getBody();
		} catch (Exception $exception) {
			throw FakturoidException::createFromLibraryExcpetion($exception);
		}
	}

	private function prepareCustomerData(Customer $customer, Document $document): void
	{
		if ($customer->isEndUser()) {
			return;
		}
		if ($customer->getBillingAddress()->getCompany() === null || trim($customer->getBillingAddress()->getCompany()) === '') {
			$customer->getBillingAddress()->setCompany($document->getBillingAddress()->getCompany());
		}
		if ($customer->getBillingAddress()->getFullName() === null || trim($customer->getBillingAddress()->getFullName()) === '') {
			$customer->getBillingAddress()->setFullName($document->getBillingAddress()->getFullName());
		}
		if ($customer->getBillingAddress()->getStreet() === null || trim($customer->getBillingAddress()->getStreet()) === '') {
			$customer->getBillingAddress()->setStreet($document->getBillingAddress()->getStreet());
		}
		if ($customer->getBillingAddress()->getCity() === null || trim($customer->getBillingAddress()->getCity()) === '') {
			$customer->getBillingAddress()->setCity($document->getBillingAddress()->getCity());
		}
		if ($customer->getBillingAddress()->getZip() === null || trim($customer->getBillingAddress()->getZip()) === '') {
			$customer->getBillingAddress()->setZip($document->getBillingAddress()->getZip());
		}
		if (trim($customer->getBillingAddress()->getCountryCode()) === '') {
			$customer->getBillingAddress()->setCountryCode($document->getBillingAddress()->getCountryCode());
		}
		if ($customer->getCompanyId() === null || trim($customer->getCompanyId()) === '') {
			$customer->setCompanyId($document->getCompanyId());
		}
		if ($customer->getVatId() === null || trim($customer->getVatId()) === '') {
			$customer->setVatId($document->getVatId());
		}
		if ($customer->getEmail() === null || trim($customer->getEmail()) === '') {
			$customer->setEmail($document->getEmail());
		}
		if ($customer->getPhone() === null || trim($customer->getPhone()) === '') {
			$customer->setPhone($document->getPhone());
		}
	}
}
