<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\Order;

use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\Shoptet\Order;
use App\Facade\InvoiceCreateFromOrderFacade;
use App\Facade\ProformaInvoiceCreateFromOrderFacade;
use App\Manager\OrderManager;
use App\Modules\Shoptet\BaseShoptetPresenter;
use App\Security\SecurityUser;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Localization\Translator;
use Nette\Utils\Html;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class OrderPresenter extends BaseShoptetPresenter
{
	public function __construct(
		private OrderManager                           $orderManager,
		private DataGridFactory                        $dataGridFactory,
		protected Translator                           $translator,
		protected InvoiceCreateFromOrderFacade         $createFromOrderFacade,
		protected ProformaInvoiceCreateFromOrderFacade $proformaInvoiceCreateFromOrderFacade
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('Shoptet:Order')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	public function handleSynchronize(int $id): void
	{
		$entity = $this->orderManager->find($this->getUser()->getProjectEntity(), $id);
		$entity = $this->orderManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $entity->getShoptetCode());
		try {
			$this->flashSuccess(
				$this->translator->translate('messages.orderList.message.synchronize.success', ['code' => $entity->getCode()])
			);
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
		$entity = $this->orderManager->find($this->getUser()->getProjectEntity(), $id);
		bdump($entity);
		$this->getTemplate()->setParameters([
			'order' => $entity,
		]);
	}

	public function handleCreateInvoice(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('orderDetail');
		}
		/** @var Order $entity */
		$entity = $this->orderManager->find($this->getUser()->getProjectEntity(), $id);

		bdump($entity);
		$invoice = $this->createFromOrderFacade->create($entity);
		$this->flashSuccess(
			$this->translator->translate(
				'messages.orderList.message.invoiceCreate.success',
				[
					'code' => $entity->getCode(),
					'link' => $this->link(':Shoptet:Invoice:detail', ['id' => $invoice->getId()]),
				]
			)
		);

		$this->redirect('this');
	}

	public function handleCreateProformaInvoice(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('orderDetail');
		}
		/** @var Order $entity */
		$entity = $this->orderManager->find($this->getUser()->getProjectEntity(), $id);

		bdump($entity);
		$invoice = $this->proformaInvoiceCreateFromOrderFacade->create($entity);
		$this->flashSuccess(
			$this->translator->translate(
				'messages.orderList.message.proformaInvoiceCreate.success',
				[
					'code' => $entity->getCode(),
					'link' => $this->link(':Shoptet:ProformaInvoice:detail', ['id' => $invoice->getId()]),
				]
			)
		);

		$this->redirect('this');
	}

	protected function createComponentOrderGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->orderManager->getRepository()->createQueryBuilder('o')
				->leftJoin('o.shippings', 'ship')
				->leftJoin('o.shippingDetail', 'sd')
				->leftJoin('o.billingAddress', 'db')
				->leftJoin('o.deliveryAddress', 'da')
				->innerJoin('o.status', 'status')
				->addSelect('ship')
				->addSelect('status')
				->addSelect('db')
				->addSelect('da')
				->addSelect('sd')
				->where('o.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);

		$grid->addColumnText('isValid', '')
			->setRenderer(function (Order $invoice): Html {
				if ($invoice->isCashDeskOrder()) {
					return
						Html::el('i')
							->class('fas fa-cash-register text-primary');
				}
				return
					Html::el('i')
						->class('text-primary fab fa-internet-explorer');
			});

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

		$options = [];
		/** @var OrderStatus $orderStatus */
		foreach ($this->getUser()->getProjectEntity()->getOrderStatuses() as $orderStatus) {
			$options[$orderStatus->getId()] = $orderStatus->getName();
		}
		$grid->addFilterSelect('statusid', 'messages.orderList.column.status', $options, 'status.id');
		$columnsStatus = $grid->addColumnStatus('status.id', 'messages.orderList.column.status')
			->setOptions($options);
		$columnsStatus->onChange[] = function (string $id, string $newStatus): void {
			$this->orderManager->changeStatus(
				$this->getUser()->getProjectEntity(),
				[$id],
				(int) $newStatus
			);
			if ($this->isAjax()) {
				$this['orderGrid']->redrawItem($id);
			}
		};

		foreach ($this->getUser()->getProjectEntity()->getOrderStatuses() as $orderStatus) {
			$columnsStatus->getOption($orderStatus->getId())
				->setClass('btn-' . $orderStatus->getType());
		}

		$grid->addGroupAction(
			'messages.orderList.changeStatus',
			$options
		)->onSelect[] = function (array $ids, $newStatus): void {
			$this->orderManager->changeStatus(
				$this->getUser()->getProjectEntity(),
				$ids,
				(int) $newStatus
			);
			if ($this->isAjax()) {
				$this->redrawControl('flashes');
				$this->redrawControl();
				$this['orderGrid']->redrawControl();
			}
		};
		$grid->addGroupAction(
			'messages.orderList.synchronize'
		)->onSelect[] = function (array $ids): void {
			foreach ($ids as $id) {
				$entity = $this->orderManager->find($this->getUser()->getProjectEntity(), (int) $id);
				$this->orderManager->synchronizeFromShoptet(
					$this->getUser()->getProjectEntity(),
					$entity->getShoptetCode()
				);
			}
			if ($this->isAjax()) {
				$this->redrawControl('flashes');
				$this->redrawControl();
				$this['orderGrid']->redrawControl();
			}
		};
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
		$grid->cantSetHiddenColumn('isValid');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
