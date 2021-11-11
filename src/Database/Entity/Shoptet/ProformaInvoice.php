<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Entity\InvoiceActionLog;
use App\Database\Entity\ProformaInvoiceActionLog;
use App\Database\Repository\Shoptet\ProformaInvoiceRepository;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: ProformaInvoiceRepository::class)]
#[ORM\Table(name: 'sf_proforma_invoice')]
#[ORM\HasLifecycleCallbacks]
class ProformaInvoice extends Document
{
	use Attributes\TId;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: ProformaInvoiceBillingAddress::class)]
	protected ?DocumentAddress $billingAddress = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: ProformaInvoiceDeliveryAddress::class)]
	protected ?DocumentAddress $deliveryAddress = null;

	/** @var ArrayCollection<int, ProformaInvoiceItem>|Collection<int, ProformaInvoiceItem> */
	#[ORM\OneToMany(mappedBy: 'document', targetEntity: ProformaInvoiceItem::class)]
	protected Collection|ArrayCollection $items;

	#[ORM\ManyToOne(targetEntity: Invoice::class)]
	#[ORM\JoinColumn(name: 'invoice_id', nullable: true, onDelete: 'SET NULL')]
	protected ?Invoice $invoice = null;

	/** @var ArrayCollection<int, ProformaInvoiceActionLog>|Collection<int, ProformaInvoiceActionLog> */
	#[ORM\OneToMany(mappedBy: 'proformaInvoice', targetEntity: ProformaInvoiceActionLog::class)]
	#[ORM\OrderBy(['createdAt' => 'DESC'])]
	protected Collection|ArrayCollection $actionLogs;

	/**
	 * @return ArrayCollection<int, ProformaInvoiceActionLog>|Collection<int, ProformaInvoiceActionLog>
	 */
	public function getActionLogs(): ArrayCollection|Collection
	{
		return $this->actionLogs;
	}

	public function getInvoice(): ?Invoice
	{
		return $this->invoice;
	}

	public function setInvoice(?Invoice $invoice): void
	{
		$this->invoice = $invoice;
	}

	public function __construct(Project $project)
	{
		parent::__construct($project);
		$this->actionLogs = new ArrayCollection();
	}
}
