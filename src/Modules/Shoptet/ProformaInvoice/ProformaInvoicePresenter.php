<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\ProformaInvoice;

use App\Api\ClientInterface;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Connector\FakturoidProformaInvoice;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\EntityManager;
use App\Facade\Fakturoid\CreateProformaInvoice;
use App\Manager\ProformaInvoiceManager;
use App\Modules\Shoptet\BaseShoptetPresenter;
use App\Savers\ProformaInvoiceSaver;
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
class ProformaInvoicePresenter extends BaseShoptetPresenter
{
	public function __construct(
		private EntityManager            $entityManager,
		private ClientInterface          $client,
		private DataGridFactory          $dataGridFactory,
		private ProformaInvoiceSaver     $invoiceSaver,
		protected Translator             $translator,
		protected ProformaInvoiceManager $invoiceManager,
		protected CreateProformaInvoice  $createProformaInvoice
	) {
		parent::__construct();
	}

	public function actionDetail(int $id): void
	{
		if ($this->isAjax()) {
			$this->redrawControl('pageDetail');
		}
		$entity = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		;
		bdump($entity);
		$this->getTemplate()->setParameters([
			'invoice' => $entity,
		]);
	}

	public function handleCreateInFakturoid(int $id): void
	{
		$invoice = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		bdump($invoice);
		//if ($invoice->getFakturoidSubjectId() === null) {
		$this->createProformaInvoice->create(invoice: $invoice);
		$this->flashSuccess(
			$this->translator->translate('messages.invoiceDetail.message.createFakturoid.success')
		);
		//} else {
		//	$this->flashWarning(
		//		$this->translator->translate('messages.invoiceDetail.message.createFakturoid.alreadyExists')
		//	);
		//}
		$this->redirect('detail', ['id' => $id]);
	}

	public function handleSynchronize(int $id): void //todo jen v nekterych pripadech!!!
	{
		/** @var ProformaInvoice $entity */
		$entity = $this->invoiceManager->find($this->getUser()->getProjectEntity(), $id);
		try {
			$invoiceData = $this->client->findProformaInvoice($entity->getCode(), $entity->getProject());
			$this->invoiceSaver->save($entity->getProject(), $invoiceData);
			$this->entityManager->refresh($entity);
			$this->redrawControl('orderDetail');
			$this->flashSuccess($this->translator->translate('messages.invoiceDetail.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->translator->translate('messages.invoiceDetail.message.synchronize.error', ['code' => $entity->getCode()]));
		}
		if ($this->isAjax()) {
			$this->redrawControl('flashes');
			$this['orderGrid']->redrawItem($id);
		} else {
			$this->redirect('this');
		}
	}

	protected function createComponentPageGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->invoiceManager->getRepository()->createQueryBuilder('i')
				->where('i.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);

		$grid->addColumnText('isValid', '')
			->setRenderer(function (ProformaInvoice $invoice): Html {
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
		$grid->addColumnDateTime('creationTime', 'messages.proformaInvoiceList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.proformaInvoiceList.column.changeTime')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addColumnText('orderCode', 'messages.proformaInvoiceList.column.orderCode')
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'messages.proformaInvoiceList.column.billingFullName')
			->setSortable();
		$grid->addColumnNumber('withVat', 'messages.proformaInvoiceList.column.withVat')
			->setSortable();
		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addAction('sync', '', 'synchronize!')//todo jen v nekterych pripadech!
		->setIcon('sync')
			->setConfirmation(
				new CallbackConfirmation(
					function (ProformaInvoice $item) use ($presenter): string {
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
