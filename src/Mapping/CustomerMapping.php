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
		private EntityManager   $entityManager,
		private CustomerManager $customerManager
	) {
	}

	private static function checkIfIsEmpty(?string $input): bool
	{
		return $input === null || $input === '';
	}

	public function mapByDocument(Document $document): ?Customer
	{
		if (self::checkIfIsEmpty($document->getVatId()) && self::checkIfIsEmpty($document->getCompanyId())) {
			return null;
		}
		$controlHash = self::computeControlHash(
			[
				(string) $document->getVatId(),
				(string) $document->getCompanyId()
			]
		);
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
			return self::computeControlHash(
				[
					(string) $customer->getVatId(),
					(string) $customer->getCompanyId()
				]
			);
		}
		return self::computeControlHash(
			[
				(string) $customer->getEmail(),
				(string) $customer->getBillingAddress()->getStreet(),
				(string) $customer->getBillingAddress()->getFullName()
			]
		);
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
}
