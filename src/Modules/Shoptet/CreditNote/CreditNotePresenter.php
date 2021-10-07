<?php

declare(strict_types=1);


namespace App\Modules\Shoptet\CreditNote;

use App\Api\ClientInterface;
use App\Application;
use App\Components\DataGridComponent\DataGridControl;
use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\Shoptet\CreditNote;
use App\Database\EntityManager;
use App\Modules\Shoptet\BaseShoptetPresenter;
use App\Savers\CreditNoteSaver;
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
class CreditNotePresenter extends BaseShoptetPresenter
{
	public function __construct(
		private EntityManager   $entityManager,
		private CreditNoteSaver $saver,
		private ClientInterface $client,
		private DataGridFactory $dataGridFactory
	) {
		parent::__construct();
	}

	public function checkRequirements(mixed $element): void
	{
		parent::checkRequirements($element);

		if (!$this->getUser()->isAllowed('Shoptet:CreditNote')) {
			$this->flashError('You cannot access this with user role');
			$this->redirect(Application::DESTINATION_FRONT_HOMEPAGE);
		}
	}

	public function handleSynchronize(int $id): void
	{
		$entity = $this->entityManager->getRepository(CreditNote::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);

		try {
			$entityData = $this->client->findCreditNote($entity->getCode(), $entity->getProject());
			$this->saver->save($entity->getProject(), $entityData);
			$this->entityManager->refresh($entity);
			$this->flashSuccess($this->getTranslator()->translate('messages.creditNoteList.message.synchronize.success', ['code' => $entity->getCode()]));
		} catch (\Throwable $exception) {
			Debugger::log($exception);
			$this->flashError($this->getTranslator()->translate('messages.creditNoteList.message.synchronize.error', ['code' => $entity->getCode()]));
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
		/** @var CreditNote $entity */
		$entity = $this->entityManager->getRepository(CreditNote::class)
			->findOneBy(['id' => $id, 'project' => $this->getUser()->getProjectEntity()]);
		bdump($entity);
		$this->getTemplate()->setParameters([
			'creditNote' => $entity,
		]);
	}


	protected function createComponentPageGrid(): DataGridControl
	{
		$grid = $this->dataGridFactory->create();
		$grid->setExportable();
		$grid->setDefaultSort(['creationTime' => 'asc']);
		$grid->setDataSource(
			$this->entityManager->getRepository(CreditNote::class)->createQueryBuilder('o')
				->where('o.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
		);
		$grid->addGroupMultiSelectAction('neco', []);
		$grid->addColumnText('isValid', '')
			->setRenderer(function (CreditNote $invoice): Html {
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
		$grid->addColumnDateTime('creationTime', 'messages.creditNoteList.column.creationTime')
			->setFormat('d.m.Y H:i')
			->setSortable();
		$grid->addColumnDateTime('changeTime', 'messages.creditNoteList.column.changeTime')
			->setFormat('d.m.Y H:i')
			->setSortable()
			->setDefaultHide(true);
		$grid->addColumnText('invoiceCode', 'messages.creditNoteList.column.invoiceCode')
			->setSortable();
		$grid->addColumnText('billingAddress.fullName', 'messages.creditNoteList.column.billingFullName')
			->setSortable();
		$grid->addColumnNumber('withVat', 'messages.creditNoteList.column.withVat')
			->setSortable();
		$grid->addAction('detail', '', 'detail')
			->setIcon('eye')
			->setClass('btn btn-xs btn-primary');

		$presenter = $this;
		$grid->addAction('sync', '', 'synchronize!')
			->setIcon('sync')
			->setConfirmation(
				new CallbackConfirmation(
					function (CreditNote $item) use ($presenter): string {
						return $presenter->translator->translate('messages.creditNoteList.synchronizeQuestion', ['code' => $item->getCode()]);
					}
				)
			);
		$grid->addFilterDateRange('creationTime', 'messages.creditNoteList.column.creationTime');
		$grid->cantSetHiddenColumn('isValid');
		$grid->cantSetHiddenColumn('code');
		$grid->setOuterFilterColumnsCount(3);
		return $grid;
	}
}
