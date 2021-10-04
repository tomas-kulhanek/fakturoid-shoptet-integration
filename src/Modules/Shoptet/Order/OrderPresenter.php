<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\Order;

use App\Api\ClientInterface;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Order;
use App\Database\EntityManager;
use App\Modules\Shoptet\BaseShoptetPresenter;
use App\Savers\OrderSaver;
use App\Security\SecurityUser;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Localization\Translator;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class OrderPresenter extends BaseShoptetPresenter
{
	public function __construct(
		private EntityManager $entityManager,
		private OrderSaver $orderSaver,
		private ClientInterface $client,
		private DataGridFactory $dataGridFactory,
		protected Translator $translator
	) {
		parent::__construct();
	}

	public function handleSynchronize(int $id): void
	{
		$entity = $this->entityManager->getRepository(Order::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);

		try {
			$orderData = $this->client->findOrder($entity->getCode(), $entity->getProject());
			$this->orderSaver->save($entity->getProject(), $orderData);
			$this->entityManager->refresh($entity);
			$this->flashSuccess($this->translator->translate('messages.orderList.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->translator->translate('messages.orderList.message.synchronize.error', ['code' => $entity->getCode()]));
		}
		$this->redrawControl('orderDetail');


		if ($this->isAjax()) {
			$this->redrawControl('flashes');
			$this['orderGrid']->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('orderDetail');
		}
		/** @var Order $entity */
		$entity = $this->entityManager->getRepository(Order::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);
		bdump($entity);
		$this->getTemplate()->setParameters([
			'order' => $entity,
		]);
	}


	protected function createComponentOrderGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->entityManager->getRepository(Order::class)->createQueryBuilder('o')
				->leftJoin('o.shippings', 'ship')
				->leftJoin('o.shippingDetail', 'sd')
				->leftJoin('o.billingAddress', 'db')
				->leftJoin('o.deliveryAddress', 'da')
				->addSelect('ship')
				->addSelect('db')
				->addSelect('da')
				->addSelect('sd')
				->where('o.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);
		$grid->addGroupMultiSelectAction('neco', []);
		$grid->addColumnText('code', '#')
			->setSortable();
		$grid->addColumnDateTime('creationTime', 'messages.orderList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.orderList.column.changeTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'messages.orderList.column.billingFullName')
			->setSortable();
		$grid->addColumnText('shippings.first.name', 'messages.orderList.column.shippingName')
			->setSortable();
		$grid->addColumnText('billingMethodName', 'messages.orderList.column.billingName')
			->setSortable();
		$grid->addColumnNumber('priceWithVat', 'messages.orderList.column.priceWithVat')
			->setSortable();
		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setConfirmation(
				new CallbackConfirmation(
					function (Order $item) use ($presenter): string {
						return $presenter->translator->translate('messages.orderList.synchronizeQuestion', ['code' => $item->getCode()]);
					}
				)
			);
		$grid->addFilterDateRange('creationTime', 'messages.orderList.column.creationTime');
		$grid->addFilterSelect('cashDeskOrder', 'messages.orderList.column.source', [0 => 'Eshop', 1 => 'Cashdesk']);

		$grid->cantSetHiddenColumn('code');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
