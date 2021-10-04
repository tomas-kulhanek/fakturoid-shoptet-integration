<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\ProformaInvoice;

use App\Api\ClientInterface;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\ProformaInvoice;
use App\Database\EntityManager;
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
		private EntityManager $entityManager,
		private ProformaInvoiceSaver $saver,
		private ClientInterface $client,
		private DataGridFactory $dataGridFactory,
		protected Translator $translator
	) {
		parent::__construct();
	}

	public function handleSynchronize(int $id): void
	{
		$entity = $this->entityManager->getRepository(ProformaInvoice::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);

		try {
			$entityData = $this->client->findProformaInvoice($entity->getCode(), $entity->getProject());
			$this->saver->save($entity->getProject(), $entityData);
			$this->entityManager->refresh($entity);
			$this->flashSuccess($this->translator->translate('messages.proformaInvoiceList.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->translator->translate('messages.proformaInvoiceList.message.synchronize.error', ['code' => $entity->getCode()]));
		}
		$this->redrawControl('pageDetail');


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
			$this->redrawControl('pageDetail');
		}
		/** @var ProformaInvoice $entity */
		$entity = $this->entityManager->getRepository(ProformaInvoice::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);
		bdump($entity);
		$this->getTemplate()->setParameters([
			'proformaInvoice' => $entity,
		]);
	}


	protected function createComponentPageGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->entityManager->getRepository(ProformaInvoice::class)->createQueryBuilder('o')
				->where('o.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);
		$grid->addGroupMultiSelectAction('neco', []);
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
		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setConfirmation(
				new CallbackConfirmation(
					function (ProformaInvoice $item) use ($presenter): string {
						return $presenter->translator->translate('messages.proformaInvoiceList.synchronizeQuestion', ['code' => $item->getCode()]);
					}
				)
			);
		$grid->addFilterDateRange('creationTime', 'messages.proformaInvoiceList.column.creationTime');
		$grid->cantSetHiddenColumn('isValid');
		$grid->cantSetHiddenColumn('code');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
