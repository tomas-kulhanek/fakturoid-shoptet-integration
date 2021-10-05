<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\Customer;

use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Customer;
use App\Manager\CustomerManager;
use App\Modules\Shoptet\BaseShoptetPresenter;
use App\Security\SecurityUser;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Localization\Translator;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class CustomerPresenter extends BaseShoptetPresenter
{
	public function __construct(
		private CustomerManager $customerManager,
		private DataGridFactory $dataGridFactory,
		protected Translator    $translator
	) {
		parent::__construct();
	}

	public function handleSynchronize(int $id): void
	{
		$entity = $this->customerManager->find($this->getUser()->getProjectEntity(), $id);
		$entity = $this->customerManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $entity->getGuid());
		try {
			$this->flashSuccess(
				$this->translator->translate('messages.customerList.message.synchronize.success')
			);
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->translator->translate('messages.customerList.message.synchronize.error'));
		}
		$this->redrawControl('customerDetail');


		if ($this->isAjax()) {
			$this->redrawControl('flashes');
			$this['pageGrid']->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('customerDetail');
		}
		/** @var Customer $entity */
		$entity = $this->customerManager->find($this->getUser()->getProjectEntity(), $id);
		bdump($entity);
		$this->getTemplate()->setParameters([
			'customer' => $entity,
		]);
	}

	protected function createComponentPageGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->customerManager->getRepository()->createQueryBuilder('o')
				->where('o.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);

		$grid->addColumnText('id', '#')
			->setSortable();
		$grid->addColumnDateTime('creationTime', 'messages.customerList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setFilterDateRange();
		$grid->addColumnDateTime('changeTime', 'messages.customerList.column.changeTime')
			->setDefaultHide(true)
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setFilterDateRange();
		$grid->addColumnText('billingAddressCompany', 'messages.customerList.column.company', 'billingAddress.company')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('vatId', 'messages.customerList.column.vatId')
			->setDefaultHide(true)
			->setSortable()
			->setFilterText();
		$grid->addColumnText('companyId', 'messages.customerList.column.companyId')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('billingAddressFullName', 'messages.customerList.column.fullName', 'billingAddress.fullName')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('billingAddressStreet', 'messages.customerList.column.street', 'billingAddress.street')
			->setDefaultHide(true)
			->setSortable()
			->setFilterText();
		$grid->addColumnText('billingAddressCity', 'messages.customerList.column.city', 'billingAddress.city')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('billingAddressDistrict', 'messages.customerList.column.district', 'billingAddress.district')
			->setDefaultHide(true)
			->setSortable()
			->setFilterText();
		$grid->addColumnText('email', 'messages.customerList.column.email')
			->setSortable()
			->setFilterText();
		$grid->addColumnText('phone', 'messages.customerList.column.phone')
			->setDefaultHide(true)
			->setSortable()
			->setFilterText();
		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setConfirmation(
				new CallbackConfirmation(
					function (Customer $item) use ($presenter): string {
						return $presenter->translator->translate('messages.customerList.synchronizeQuestion', ['code' => $item->getEmail()]);
					}
				)
			);

		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
