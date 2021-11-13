<?php

namespace App\Modules\App\History;

use App\Components\DataGridComponent\DataGridFactory;
use App\Database\Entity\ActionLog;
use App\Database\Entity\CustomerActionLog;
use App\Database\Entity\InvoiceActionLog;
use App\Database\Entity\ProformaInvoiceActionLog;
use App\Database\Entity\Shoptet\Customer;
use App\Database\Entity\Shoptet\Document;
use App\Database\Entity\User;
use App\Modules\App\BaseAppPresenter;
use App\Security\SecurityUser;
use Doctrine\ORM\EntityManagerInterface;
use Nette\Bridges\ApplicationLatte\DefaultTemplate;
use Nette\Utils\Html;
use Ublaboo\DataGrid\DataGrid;

/**
 * @method DefaultTemplate getTemplate()
 * @method SecurityUser getUser()
 */
final class HistoryPresenter extends BaseAppPresenter
{
	public function __construct(
		private DataGridFactory        $dataGridFactory,
		private EntityManagerInterface $entityManager
	) {
		parent::__construct();
	}

	protected function createComponentHistoryGrid(): DataGrid
	{
		$grid = $this->dataGridFactory->create(true, false);
		$grid->setDataSource(
			$this->entityManager->getRepository(ActionLog::class)
				->createQueryBuilder('a')
				->addSelect('u')
				->leftJoin('a.user', 'u')
				->where('a.project = :project')
				->setParameter('project', $this->getUser()->getProjectEntity())
				->orderBy('a.createdAt', 'DESC')
		);
		$grid->addColumnDateTime('createdAt', 'Datum a čas');
		$grid->addColumnText('referenceCode', 'Referenční kód');
		$grid->addColumnText('type', 'Aktivita')
			->setRenderer(
				fn (ActionLog $actionLog) => $this->getTranslator()->translate(sprintf('messages.actionLog.' . $actionLog->getType()))
			);
		$grid->addColumnText('user', 'Uživatel', 'user.email')
			->setRenderer(function (ActionLog $actionLog): string {
				if ($actionLog->getUser() instanceof User) {
					return $actionLog->getUser()->getName();
				}
				return 'Automat';
			});

		$grid->addColumnText('actionLogType', 'Document Type')
			->setRenderer(
				fn (ActionLog $actionLog) => $this->getTranslator()->translate(sprintf('messages.actionLog.documents.' . $actionLog->getActionLogType()))
			);

		$grid->addColumnText('status', 'Stav')
			->setRenderer(
				function (ActionLog $actionLog): Html {
					if ($actionLog->isError()) {
						return Html::el('span')
							->class('badge bg-danger')
							->addText('Chyba');
					}
					return Html::el('span')
						->class('badge bg-success')
						->addText('Úspěch');
				}
			);
		$grid->setItemsDetail(__DIR__ . '/templates/grid.itemDetails.latte');
		$grid->getItemsDetail()->setRenderCondition(fn (ActionLog $actionLog) => $actionLog->getMessage() !== null);

		return $grid;
	}
}
