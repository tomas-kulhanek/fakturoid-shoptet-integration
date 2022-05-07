<?php

declare(strict_types=1);


namespace App\Manager;

use App\Api\ClientInterface;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\CustomerBillingAddress;
use App\Database\Entity\Shoptet\CustomerDeliveryAddress;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\Database\Repository\Shoptet\CustomerRepository;
use App\DTO\Shoptet\Customer\CustomerResponse;
use App\Savers\Shoptet\CustomerSaver;
use Doctrine\Common\Collections\ArrayCollection;

class CustomerManager
{
	public function __construct(
		private EntityManager            $entityManager,
		private ClientInterface          $shoptetClient,
		private CustomerSaver            $customerSaver
	) {
	}

	public function getEndUser(Project $project): Customer
	{
		return $this->entityManager->getRepository(Customer::class)
			->findOneBy(['project' => $project, 'endUser' => true]);
	}


	public function createFromDocument(Document $document): Customer
	{
		$customer = new Customer($document->getProject());
		$this->entityManager->persist($customer);

		$customer->setCreationTime(new \DateTimeImmutable());
		$customer->setCompanyId($document->getCompanyId());
		$customer->setVatId($document->getVatId());
		$customer->setEmail($document->getEmail());
		$customer->setPhone($document->getPhone());

		$customerBillingAddress = new CustomerBillingAddress();
		$this->entityManager->persist($customerBillingAddress);

		$customerBillingAddress->setCompany($document->getBillingAddress()->getCompany());
		$customerBillingAddress->setFullName($document->getBillingAddress()->getFullName());
		$customerBillingAddress->setStreet($document->getBillingAddress()->getStreet());
		$customerBillingAddress->setHouseNumber($document->getBillingAddress()->getHouseNumber());
		$customerBillingAddress->setCity($document->getBillingAddress()->getCity());
		$customerBillingAddress->setDistrict($document->getBillingAddress()->getDistrict());
		$customerBillingAddress->setAdditional($document->getBillingAddress()->getAdditional());
		$customerBillingAddress->setZip($document->getBillingAddress()->getZip());
		$customerBillingAddress->setCountryCode($document->getBillingAddress()->getCountryCode());
		$customerBillingAddress->setRegionName($document->getBillingAddress()->getRegionName());
		$customerBillingAddress->setRegionShortcut($document->getBillingAddress()->getRegionShortcut());
		$customer->setBillingAddress($customerBillingAddress);
		if (!$document->isAddressesEqual()) {
			$customerDeliveryAddress = new CustomerDeliveryAddress();
			$this->entityManager->persist($customerDeliveryAddress);
			$customerDeliveryAddress->setCompany($document->getDeliveryAddress()->getCompany());
			$customerDeliveryAddress->setCustomer($customer);
			$customerDeliveryAddress->setFullName($document->getDeliveryAddress()->getFullName());
			$customerDeliveryAddress->setStreet($document->getDeliveryAddress()->getStreet());
			$customerDeliveryAddress->setHouseNumber($document->getDeliveryAddress()->getHouseNumber());
			$customerDeliveryAddress->setCity($document->getDeliveryAddress()->getCity());
			$customerDeliveryAddress->setDistrict($document->getDeliveryAddress()->getDistrict());
			$customerDeliveryAddress->setAdditional($document->getDeliveryAddress()->getAdditional());
			$customerDeliveryAddress->setZip($document->getDeliveryAddress()->getZip());
			$customerDeliveryAddress->setCountryCode($document->getDeliveryAddress()->getCountryCode());
			$customerDeliveryAddress->setRegionName($document->getDeliveryAddress()->getRegionName());
			$customerDeliveryAddress->setRegionShortcut($document->getDeliveryAddress()->getRegionShortcut());
			$customer->setDeliveryAddress(new ArrayCollection());
			$customer->getDeliveryAddress()->add($customerDeliveryAddress);
		}

		return $customer;
	}

	public function createFromOrder(Order $order): Customer
	{
		$customer = new Customer($order->getProject());
		$this->entityManager->persist($customer);

		$customer->setCreationTime(new \DateTimeImmutable());
		$customer->setCompanyId($order->getCompanyId());
		$customer->setVatId($order->getVatId());
		$customer->setEmail($order->getEmail());
		$customer->setPhone($order->getPhone());

		$customerBillingAddress = new CustomerBillingAddress();
		$this->entityManager->persist($customerBillingAddress);

		$customerBillingAddress->setCompany($order->getBillingAddress()->getCompany());
		$customerBillingAddress->setFullName($order->getBillingAddress()->getFullName());
		$customerBillingAddress->setStreet($order->getBillingAddress()->getStreet());
		$customerBillingAddress->setHouseNumber($order->getBillingAddress()->getHouseNumber());
		$customerBillingAddress->setCity($order->getBillingAddress()->getCity());
		$customerBillingAddress->setDistrict($order->getBillingAddress()->getDistrict());
		$customerBillingAddress->setAdditional($order->getBillingAddress()->getAdditional());
		$customerBillingAddress->setZip($order->getBillingAddress()->getZip());
		$customerBillingAddress->setCountryCode($order->getBillingAddress()->getCountryCode());
		$customerBillingAddress->setRegionName($order->getBillingAddress()->getRegionName());
		$customerBillingAddress->setRegionShortcut($order->getBillingAddress()->getRegionShortcut());
		$customer->setBillingAddress($customerBillingAddress);
		$flushEntities = [$customer, $customerBillingAddress];
		if (!$order->isAddressesEqual()) {
			$customerDeliveryAddress = new CustomerDeliveryAddress();
			$this->entityManager->persist($customerDeliveryAddress);
			$customerDeliveryAddress->setCompany($order->getDeliveryAddress()->getCompany());
			$customerDeliveryAddress->setCustomer($customer);
			$customerDeliveryAddress->setFullName($order->getDeliveryAddress()->getFullName());
			$customerDeliveryAddress->setStreet($order->getDeliveryAddress()->getStreet());
			$customerDeliveryAddress->setHouseNumber($order->getDeliveryAddress()->getHouseNumber());
			$customerDeliveryAddress->setCity($order->getDeliveryAddress()->getCity());
			$customerDeliveryAddress->setDistrict($order->getDeliveryAddress()->getDistrict());
			$customerDeliveryAddress->setAdditional($order->getDeliveryAddress()->getAdditional());
			$customerDeliveryAddress->setZip($order->getDeliveryAddress()->getZip());
			$customerDeliveryAddress->setCountryCode($order->getDeliveryAddress()->getCountryCode());
			$customerDeliveryAddress->setRegionName($order->getDeliveryAddress()->getRegionName());
			$customerDeliveryAddress->setRegionShortcut($order->getDeliveryAddress()->getRegionShortcut());
			$customer->setDeliveryAddress(new ArrayCollection());
			$customer->getDeliveryAddress()->add($customerDeliveryAddress);
			$flushEntities[] = $customerDeliveryAddress;
		}

		return $customer;
	}

	public function getRepository(): CustomerRepository
	{
		/** @var CustomerRepository $repository */
		$repository = $this->entityManager->getRepository(Customer::class);
		return $repository;
	}

	public function find(Project $project, int $id): ?Customer
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'id' => $id]);
	}

	public function findByGuid(Project $project, string $guid): ?Customer
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'guid' => $guid]);
	}

	public function findByShoptetGuid(Project $project, string $guid): ?Customer
	{
		return $this->getRepository()
			->findOneBy(['project' => $project, 'shoptetGuid' => $guid]);
	}

	public function synchronizeFromShoptet(Project $project, string $id): ?Customer
	{
		$customerData = $this->shoptetClient->findCustomer($id, $project);
		if (!$customerData->data instanceof CustomerResponse) {
			return null;
		}
		bdump($customerData);
		return $this->customerSaver->save($project, $customerData->data->customer);
	}
}
