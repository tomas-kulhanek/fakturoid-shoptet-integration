<?php

declare(strict_types=1);


namespace App\Modules\App\Order;

use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\OrderStatus;
use App\Database\Entity\Shoptet\Order;
use App\Database\Entity\Shoptet\OrderItem;
use App\Database\Entity\Shoptet\ReceivedWebhook;
use App\DTO\Shoptet\Request\Webhook;
use App\Facade\InvoiceCreateFacade;
use App\Facade\ProformaInvoiceCreateFacade;
use App\Latte\NumberFormatter;
use App\Manager\OrderManager;
use App\MessageBus\MessageBusDispatcher;
use App\MessageBus\SynchronizeMessageBusDispatcher;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use App\UI\Form;
use App\UI\FormFactory;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\DI\Attributes\Inject;
use Nette\Forms\Controls\SubmitButton;
use Nette\Localization\Translator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Html;
use Tracy\Debugger;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class OrderPresenter extends BaseAppPresenter
{
	#[Inject]
	public NumberFormatter $numberFormatter;

	#[Inject]
	public SynchronizeMessageBusDispatcher $synchronizeMessageBusDispatcher;

	#[Inject]
	public MessageBusDispatcher $messageBusDispatcher;

	private ?Order $order = null;

	#[Inject]
	public FormFactory $formFactory;

	public function __construct(
		private OrderManager                  $orderManager,
		private DataGridFactory               $dataGridFactory,
		protected InvoiceCreateFacade         $createFromOrderFacade,
		protected ProformaInvoiceCreateFacade $ProformaInvoiceCreateFacade
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed(\App\Security\Authorizator\StaticAuthorizator::RESOURCE_ORDER)) {
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
				$this->getTranslator()->translate('messages.orderList.message.synchronize.success', ['code' => $entity->getCode()])
			);
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->getTranslator()->translate('messages.orderList.message.synchronize.error', ['code' => $entity->getCode()]));
		}
		$this->redrawControl('orderDetail');


		if ($this->isAjax()) {
			$this->redrawControl('flashes');
			$this['orderGrid']->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}

	public function handleSynchronizeAll(): void
	{
		try {
			$this->synchronizeMessageBusDispatcher->dispatchOrder($this->getUser()->getProjectEntity(), $this->getUser()->getProjectEntity()->getLastOrderSyncAt());
			$this->getUser()->getProjectEntity()->setLastOrderSyncAt(new \DateTimeImmutable());
			$this->flashSuccess(
				$this->getTranslator()->translate('messages.orderList.message.synchronizeAll.success')
			);
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->getTranslator()->translate('messages.orderList.message.synchronizeAll.error'));
		}
		$this->redrawControl('orderDetail');


		if ($this->isAjax()) {
			$this->redrawControl('flashes');
		} else {
			$this->redirect('this');
		}
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('orderDetail');
		}
		$this->order = $this->orderManager->find($this->getUser()->getProjectEntity(), $id);
		bdump($this->order);
		$this->getTemplate()->setParameters([
			'order' => $this->order,
		]);
	}

	protected function createComponentOrderGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'desc']);
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
			->setDefaultHide(true)
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'messages.orderList.column.billingFullName')
			->setSortable();
		$grid->addColumnText('shippings.first.name', 'messages.orderList.column.shippingName')
			->setDefaultHide(true);
		$grid->addColumnText('billingMethod', 'messages.orderList.column.billingName', 'billingMethodId')
			->setDefaultHide(true)
			->setSortable();
		$grid->addColumnNumber('priceWithVat', 'messages.orderList.column.priceWithVat', 'mainPriceWithVat')
			->setSortable()
			->setRenderer(fn (Order $order) => $this->numberFormatter->__invoke($order->getPriceWithVat(), $order->getPriceCurrencyCode()));
		$options = [];
		/** @var OrderStatus $orderStatus */
		foreach ($this->getUser()->getProjectEntity()->getOrderStatuses() as $orderStatus) {
			$options[$orderStatus->getId()] = $orderStatus->getName();
		}
		$columnsStatus = $grid->addColumnStatus('status.id', 'messages.orderList.column.status')
			->setOptions($options);
		$columnsStatus->onChange[] = function (string $id, string $newStatus): void {
			$this->orderManager->changeStatus(
				$this->getUser()->getProjectEntity(),
				[$id],
				(int)$newStatus
			);
			if ($this->isAjax()) {
				$this['orderGrid']->redrawItem($id);
			}
		};

		foreach ($this->getUser()->getProjectEntity()->getOrderStatuses() as $orderStatus) {
			$columnsStatus->getOption($orderStatus->getId())
				->setClass('btn-' . $orderStatus->getType());
		}
		$columnsStatus->setRenderCondition(fn (Order $order) => !$order->getDeletedAt() instanceof \DateTimeImmutable);
		$grid->setRowCallback(function (Order $order, Html $tr): void {
			if ($order->getDeletedAt() instanceof \DateTimeImmutable) {
				$tr->addClass('bg-danger');
			}
		});
		$grid->addGroupAction(
			'messages.orderList.changeStatus',
			$options
		)->onSelect[] = function (array $ids, $newStatus): void {
			$this->orderManager->changeStatus(
				$this->getUser()->getProjectEntity(),
				$ids,
				(int)$newStatus
			);
			if ($this->isAjax()) {
				$this->redrawControl('flashes');
				$this->redrawControl();
				$this['orderGrid']->redrawControl();
			}
		};
		$grid->allowRowsGroupAction(fn (Order $order) => !$order->getDeletedAt() instanceof \DateTimeImmutable);

		$grid->addGroupAction(
			'messages.orderList.synchronize'
		)->onSelect[] = function (array $ids): void {
			foreach ($ids as $id) {
				$entity = $this->orderManager->find($this->getUser()->getProjectEntity(), (int)$id);
				$request = new ReceivedWebhook(
					$this->getUser()->getProjectEntity(),
					$this->getUser()->getProjectEntity()->getEshopId(),
					Webhook::TYPE_ORDER_UPDATE,
					$entity->getShoptetCode(),
					new \DateTimeImmutable()
				);
				$this->messageBusDispatcher->dispatch($request);
			}
			if ($this->isAjax()) {
				$this->redrawControl('flashes');
				$this->redrawControl();
				$this['orderGrid']->redrawControl();
			}
		};

		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setRenderCondition(fn (Order $document) => $document->getShoptetCode() !== null && $document->getShoptetCode() !== '' && !$document->getDeletedAt() instanceof \DateTimeImmutable)
			->setConfirmation(
				new CallbackConfirmation(
					fn(Order $item): string => $this->translator->translate('messages.orderList.synchronizeQuestion', ['code' => $item->getCode()])
				)
			);
		$grid->addFilterDateRange('creationTime', 'messages.orderList.column.creationTime');
		$grid->addFilterDateRange('changeTime', 'messages.orderList.column.changeTime');
		$grid->addFilterText('code', 'messages.orderList.column.code');
		$grid->addFilterText('billingAddressFullName', 'messages.orderList.column.billingFullName', ['db.fullName']);
		$grid->addFilterSelect('statusid', 'messages.orderList.column.status', $options, 'status.id')
			->setPrompt('');
		$grid->addFilterSelect('cashDeskOrder', 'messages.orderList.column.source', [0 => 'Eshop', 1 => 'Cashdesk'])
			->setPrompt('');

		$grid->addToolbarButton('synchronizeAll!', 'messages.orderList.synchronizeAll');

		$grid->cantSetHiddenColumn('code');
		$grid->cantSetHiddenColumn('isValid');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}

	protected function createComponentOrderDetail(): Form
	{
		$form = $this->formFactory->create();

		$form->addSubmit('createInvoice', '')
			->getControlPrototype()->class('btn btn-warning float-right');
		$form->addSubmit('createProformaInvoice', '')
			->getControlPrototype()->class('btn btn-warning float-right');

		$form->addSubmit('synchronize', '')
			->getControlPrototype()->class('btn btn-warning float-right');

		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('synchronize');
			if (!$button->isSubmittedBy()) {
				return;
			}
			$this->orderManager->synchronizeFromShoptet($this->getUser()->getProjectEntity(), $this->order->getShoptetCode());
			try {
				$this->flashSuccess(
					$this->getTranslator()->translate('messages.orderList.message.synchronize.success', ['code' => $this->order->getCode()])
				);
			} catch (\Throwable $exception) {
				Debugger::log($exception);
				$this->flashError($this->getTranslator()->translate('messages.orderList.message.synchronize.error', ['code' => $this->order->getCode()]));
			}
			$this->redrawControl('orderDetail');
			$this->redirect('this');
		};

		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('createInvoice');
			if (!$button->isSubmittedBy()) {
				return;
			}
			$invoice = $this->createFromOrderFacade->createFromOrder($this->order);
			$this->flashSuccess(
				$this->getTranslator()->translate(
					'messages.orderList.message.invoiceCreate.success',
					[
						'code' => $this->order->getCode(),
						'link' => $this->link(':App:Invoice:detail', ['id' => $invoice->getId()]),
					]
				)
			);

			$this->redirect('this');
		};


		$form->onSuccess[] = function (Form $form, ArrayHash $arrayHash): void {
			/** @var SubmitButton $button */
			$button = $form->getComponent('createProformaInvoice');
			if (!$button->isSubmittedBy()) {
				return;
			}
			$invoice = $this->ProformaInvoiceCreateFacade->createFromOrder($this->order, $arrayHash->items);
			$this->flashSuccess(
				$this->getTranslator()->translate(
					'messages.orderList.message.createProformaInvoice.success',
					[
						'code' => $this->order->getCode(),
						'link' => $this->link(':App:Invoice:detail', ['id' => $invoice->getId()]),
					]
				)
			);

			$this->redirect('this');
		};


		return $form;
	}
}
