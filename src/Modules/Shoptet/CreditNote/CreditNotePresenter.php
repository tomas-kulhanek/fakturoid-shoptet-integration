<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\CreditNote;

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
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class CreditNotePresenter extends BaseShoptetPresenter
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

	public function handleOrderSynchronize(int $id): void
	{
		$order = $this->entityManager->getRepository(Order::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);
		$orderData = $this->client->findOrder($order->getCode(), $order->getProject());
		$this->orderSaver->save($order->getProject(), $orderData);
		$this->entityManager->refresh($order);
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
		$this->getTemplate()->setParameters([
			'order' => $this->entityManager->getRepository(Order::class)
				->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]),
		]);
	}


	protected function createComponentOrderGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'desc', 'code' => 'desc']);
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
		$grid->addColumnDateTime('creationTime', 'Created')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'Last change')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'Name')
			->setSortable();
		$grid->addColumnText('shippings.first.name', 'Shippings')
			->setSortable();
		$grid->addColumnText('billingMethodName', 'Billing')
			->setSortable();
		$grid->addColumnNumber('priceWithVat', 'Price')
			->setSortable();
		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addAction('sync', '', 'orderSynchronize!')
			->setIcon('sync')
			->setConfirmation(
				new CallbackConfirmation(
					function (Order $item) use ($presenter): string {
						return $presenter->translator->translate('Do you really want to synchronize order %code%?', ['code' => $item->getCode()]);
					}
				)
			);
		$grid->addFilterDateRange('creationTime', 'Creation date');
		$grid->addFilterSelect('cashDeskOrder', 'Source', [0 => 'Eshop', 1 => 'Cashdesk']);
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
