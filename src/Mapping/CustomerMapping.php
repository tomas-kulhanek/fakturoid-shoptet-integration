<?php

declare(strict_types=1);


namespace App\Mapping;

use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\DocumentAddress;
use App\Database\Entity\Shoptet\InvoiceBillingAddress;
use App\Database\Entity\Shoptet\Order;
use App\Database\EntityManager;
use App\Manager\CustomerManager;
use Nette\Utils\Strings;

class CustomerMapping
{
	public function __construct(
		private EntityManager $entityManager,
		private CustomerManager $customerManager
	) {
	}

	private static function checkIfIsEmpty(?string $input): bool
	{
		return $input === null || $input === '';
	}

	public function mapByOrder(Order $order): Customer
	{
		if (!self::checkIfIsEmpty($order->getVatId()) || !self::checkIfIsEmpty($order->getCompanyId())) {
			$controlHash = self::getControlHashCompany((string) $order->getVatId(), (string) $order->getCompanyId());
		} else {
			$controlHash = self::getControlHashPerson(
				(string) $order->getEmail(),
				(string) $order->getBillingAddress()->getStreet(),
				(string) $order->getBillingAddress()->getFullName()
			);
		}
		$customer = $this->entityManager->getRepository(Customer::class)
			->findOneBy([
				'project' => $order->getProject(),
				'controlHash' => $controlHash,
			]);
		if (!$customer instanceof Customer) {
			$customer = $this->customerManager->createFromOrder($order);
		}

		return $customer;
	}

	public function mapByDocument(Document $document): Customer
	{
		$controlHash = '';
		if (!self::checkIfIsEmpty($document->getVatId()) || !self::checkIfIsEmpty($document->getCompanyId())) {
			$controlHash = self::getControlHashCompany((string) $document->getVatId(), (string) $document->getCompanyId());
		} elseif ($document->getBillingAddress() instanceof DocumentAddress) {
			$controlHash = self::getControlHashPerson(
				(string) $document->getEmail(),
				(string) $document->getBillingAddress()->getStreet(),
				(string) $document->getBillingAddress()->getFullName()
			);
		}
		$customer = $this->entityManager->getRepository(Customer::class)
			->findOneBy([
				'project' => $document->getProject(),
				'controlHash' => $controlHash,
			]);
		if (!$customer instanceof Customer) {
			$customer = $this->customerManager->createFromDocument($document);
		}

		return $customer;
	}

	public static function getControlHashFromCustomer(Customer $customer): string
	{
		if (!self::checkIfIsEmpty($customer->getVatId()) || !self::checkIfIsEmpty($customer->getCompanyId())) {
			return self::getControlHashCompany((string) $customer->getVatId(), (string) $customer->getCompanyId());
		}
		return self::getControlHashPerson(
			(string) $customer->getEmail(),
			(string) $customer->getBillingAddress()->getStreet(),
			(string) $customer->getBillingAddress()->getFullName()
		);
	}

	public static function getControlHashCompany(string $companyId, string $vatId): string
	{
		return self::computeControlHash([
			$companyId, $vatId,
		]);
	}

	/**
	 * @param string[] $input
	 * @return string
	 */
	private static function computeControlHash(array $input): string
	{
		return Strings::substring(
			s: sha1(
				Strings::webalize(implode('-', $input))
			),
			start: 0,
			length: 255
		);
	}

	public static function getControlHashPerson(string $email, string $street, string $fullName): string
	{
		return self::computeControlHash([
			$email,
			$street,
			$fullName,
		]);
	}
}
