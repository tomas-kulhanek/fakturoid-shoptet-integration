<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\Invoice;

use App\Api\ClientInterface;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\Invoice;
use App\Database\Entity\Shoptet\Order;
use App\Database\EntityManager;
use App\Modules\Shoptet\BaseShoptetPresenter;
use App\Savers\InvoiceSaver;
use App\Savers\OrderSaver;
use App\Security\SecurityUser;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Localization\Translator;
use Nette\Utils\Html;
use Ublaboo\DataGrid\Column\Action\Confirmation\CallbackConfirmation;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
class InvoicePresenter extends BaseShoptetPresenter
{
	public function __construct(
		private EntityManager $entityManager,
		private InvoiceSaver $invoiceSaver,
		private ClientInterface $client,
		private DataGridFactory $dataGridFactory,
		protected Translator $translator
	)
	{
		parent::__construct();
	}

	public function handleSynchronize(int $id): void
	{
		$order = $this->entityManager->getRepository(Invoice::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);
		$invoiceData = $this->client->findInvoice($order->getCode(), $order->getProject());
		$this->invoiceSaver->save($order->getProject(), $invoiceData);
		$this->entityManager->refresh($order);
		$this->redrawControl('orderDetail');
// todo message!
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
			'invoice' => $this->entityManager->getRepository(Invoice::class)
				->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]),
		]);
	}


	protected function createComponentOrderGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->entityManager->getRepository(Invoice::class)->createQueryBuilder('i')
				->where('i.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);
		$grid->addGroupMultiSelectAction('neco', []);
		$grid->addColumnText('isValid', '')
			->setRenderer(function (Invoice $invoice) {
				if ($invoice->isValid()) {
					return
						Html::el('i')
							->class('fa fa-check-circle text-success');
				}
				return
					Html::el('i')
						->class('text-danger fa fa-times-circle');
			});
		$grid->addColumnText('code', '#')
			->setSortable();
		$grid->addColumnDateTime('creationTime', 'messages.invoiceList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnText('orderCode', 'messages.invoiceList.column.orderCode')
			->setSortable();
		$grid->addColumnText('proformaInvoiceCode', 'messages.invoiceList.column.proformaInvoiceCode')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.invoiceList.column.changeTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnDateTime('dueDate', 'messages.invoiceList.column.dueDate')
			->setFormat('d.m.Y')
			->setSortable();
		$grid->addColumnDateTime('taxDate', 'messages.invoiceList.column.taxDate')
			->setFormat('d.m.Y')
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'messages.invoiceList.column.billingFullName')
			->setSortable();
		$grid->addColumnText('toPay', 'messages.invoiceList.column.toPay')
			->setSortable();
		$grid->addColumnNumber('withVat', 'messages.invoiceList.column.withVat')
			->setSortable();
		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setConfirmation(
				new CallbackConfirmation(
					function (Invoice $item) use ($presenter): string {
						return $presenter->translator->translate('messages.invoiceList.synchronizeQuestion', ['code' => $item->getCode()]);
					}
				)
			);


		$grid->addFilterDateRange('creationTime', 'messages.invoiceList.column.creationTime');

		$grid->addFilterText('orderCode', 'messages.invoiceList.column.orderCode');
		$grid->addFilterText('proformaInvoiceCode', 'messages.invoiceList.column.proformaInvoiceCode');

		$grid->addFilterDateRange('changeTime', 'messages.invoiceList.column.changeTime');
		$grid->addFilterDateRange('dueDate', 'messages.invoiceList.column.dueDate');
		$grid->addFilterDateRange('taxDate', 'messages.invoiceList.column.taxDate');

		$grid->cantSetHiddenColumn('isValid');
		$grid->cantSetHiddenColumn('code');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
