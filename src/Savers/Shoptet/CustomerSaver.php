<?php

declare(strict_types=1);


namespace App\Savers\Shoptet;

use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\CustomerAddress;
use App\Database\Entity\Shoptet\CustomerBillingAddress;
use App\Database\Entity\Shoptet\CustomerDeliveryAddress;
use App\Database\Entity\Shoptet\Project;
use App\Database\EntityManager;
use App\DTO;
use Doctrine\ORM\NoResultException;

class CustomerSaver
{
	public function __construct(
		private EntityManager $entityManager
	) {
	}

	protected function pairByCodeAndProject(Project $project, string $guid): Customer
	{
		$qb = $this->entityManager->createQueryBuilder()
			->from(Customer::class, 'c')
			->select('c')
			->where('c.guid = :guid')
			->andWhere('c.project = :project')
			->setParameter('guid', $guid)
			->setParameter('project', $project);
		try {
			$customer = $qb->getQuery()->getSingleResult();
		} catch (NoResultException) {
			$customer = new Customer($project, $guid);
			$this->entityManager->persist($customer);
		}

		return $customer;
	}

	public function save(Project $project, DTO\Shoptet\Customer\Customer $customer): Customer
	{
		$customerEntity = $this->pairByCodeAndProject($project, $customer->guid);
		if ($customer->changeTime instanceof \DateTimeImmutable) {
			if ($customerEntity->getChangeTime() instanceof \DateTimeImmutable && $customerEntity->getChangeTime() >= $customer->changeTime) {
				return $customerEntity;
			}
		}
		$this->fillBasicData($customerEntity, $customer);

		$this->processDeliveryAddresses($customerEntity, $customer);
		if (!$customerEntity->getBillingAddress() instanceof CustomerAddress) {
			$customerEntity->setBillingAddress(new CustomerBillingAddress());
			$customerEntity->getBillingAddress()->setCustomer($customerEntity);
			$this->entityManager->persist($customerEntity->getBillingAddress());
		}
		$this->fillAddress($customerEntity->getBillingAddress(), $customer->billingAddress);
		bdump($customer);
		$this->entityManager->flush();
		return $customerEntity;
	}


	protected function processDeliveryAddresses(Customer $customer, DTO\Shoptet\Customer\Customer $dtoCustomer): void
	{
		$hashes = [];
		/** @var DTO\Shoptet\Customer\CustomerAddress $item */
		foreach ($dtoCustomer->deliveryAddress as $item) {
			$hashes[] = $item->getControlHash();
		}
		$persistedEntities = [];
		/** @var CustomerDeliveryAddress $entity */
		foreach ($customer->getDeliveryAddress() as $entity) {
			if (!in_array($entity->getControlHash(), $hashes, true)) {
				$customer->getDeliveryAddress()->removeElement($entity);
				$this->entityManager->remove($entity);
				continue;
			}
			$persistedEntities[$entity->getControlHash()] = $entity;
		}

		foreach ($dtoCustomer->deliveryAddress as $item) {
			if (isset($persistedEntities[$item->getControlHash()])) {
				$entity = $persistedEntities[$item->getControlHash()];
			} else {
				$entity = new CustomerDeliveryAddress();
				$entity->setCustomer($customer);
				$this->entityManager->persist($entity);
				$customer->getDeliveryAddress()->add($entity);
			}

			$this->fillAddress($entity, $item);
		}
	}

	private function fillAddress(CustomerAddress $customerAddress, DTO\Shoptet\Customer\CustomerAddress $dto): void
	{
		$customerAddress->setCompany($dto->company);
		$customerAddress->setFullName($dto->fullName);
		$customerAddress->setStreet($dto->street);
		$customerAddress->setHouseNumber($dto->houseNumber);
		$customerAddress->setCity($dto->city);
		$customerAddress->setDistrict($dto->district);
		$customerAddress->setAdditional($dto->additional);
		$customerAddress->setZip($dto->zip);
		$customerAddress->setCountryCode($dto->countryCode);
		$customerAddress->setRegionName($dto->regionName);
		$customerAddress->setRegionShortcut($dto->regionShortcut);
	}


	private function fillBasicData(Customer $customer, DTO\Shoptet\Customer\Customer $dtoCustomer): void
	{
		$customer->setCreationTime($dtoCustomer->creationTime);
		$customer->setChangeTime($dtoCustomer->changeTime);
		$customer->setCompanyId($dtoCustomer->companyId);
		$customer->setVatId($dtoCustomer->vatId);
		$customer->setClientCode($dtoCustomer->clientCode);
		$customer->setRemark($dtoCustomer->remark);
		$customer->setPriceRatio($dtoCustomer->priceRatio);
		$customer->setBirthDate($dtoCustomer->birthDate);
		$customer->setDisabledOrders($dtoCustomer->disabledOrders);
		$customer->setAdminUrl($dtoCustomer->adminUrl);

		foreach ($dtoCustomer->accounts as $account) {
			if ($account->mainAccount) {
				$customer->setEmail($account->email);
				$customer->setPhone($account->phone);
				break;
			}
		}
	}
}
