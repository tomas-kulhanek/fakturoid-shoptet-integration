<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Entity\InvoiceActionLog;
use App\Database\Repository\Shoptet\InvoiceRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: InvoiceRepository::class)]
#[ORM\Table(name: 'sf_invoice')]
#[ORM\HasLifecycleCallbacks]
class Invoice extends Document
{
	use Attributes\TId;

	//todo shoptet nema u faktur PAID
	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $proformaInvoiceCode = null;

	#[ORM\ManyToOne(targetEntity: ProformaInvoice::class, cascade: ['persist'])]
	#[ORM\JoinColumn(name: 'proforma_invoice_id', nullable: true, onDelete: 'SET NULL')]
	protected ?ProformaInvoice $proformaInvoice = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $taxDate = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $documentRemark = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: InvoiceBillingAddress::class, cascade: ['persist', 'remove'])]
	protected ?DocumentAddress $billingAddress = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: InvoiceDeliveryAddress::class, cascade: ['persist', 'remove'])]
	protected ?DocumentAddress $deliveryAddress = null;

	/** @var ArrayCollection<int, InvoiceItem|DocumentItem>|Collection<int, InvoiceItem|DocumentItem> */
	#[ORM\OneToMany(mappedBy: 'document', targetEntity: InvoiceItem::class, cascade: ['persist', 'remove'])]
	protected Collection|ArrayCollection $items;

	#[ORM\OneToOne(mappedBy: 'invoice', targetEntity: InvoiceEET::class, cascade: ['persist', 'remove'])]
	protected ?InvoiceEET $eet = null;

	/** @var ArrayCollection<int, InvoiceActionLog>|Collection<int, InvoiceActionLog> */
	#[ORM\OneToMany(mappedBy: 'invoice', targetEntity: InvoiceActionLog::class, cascade: ['persist', 'remove'])]
	#[ORM\OrderBy(['createdAt' => 'DESC'])]
	protected Collection|ArrayCollection $actionLogs;

	/**
	 * @return ArrayCollection<int, InvoiceActionLog>|Collection<int, InvoiceActionLog>
	 */
	public function getActionLogs(): ArrayCollection|Collection
	{
		return $this->actionLogs;
	}

	public function setProformaInvoiceCode(?string $proformaInvoiceCode): void
	{
		$this->proformaInvoiceCode = $proformaInvoiceCode;
	}

	public function setTaxDate(?DateTimeImmutable $taxDate): void
	{
		$this->taxDate = $taxDate;
	}

	public function setDocumentRemark(?string $documentRemark): void
	{
		$this->documentRemark = $documentRemark;
	}

	public function getProformaInvoiceCode(): ?string
	{
		return $this->proformaInvoiceCode;
	}

	public function getTaxDate(): ?DateTimeImmutable
	{
		return $this->taxDate;
	}

	public function getDocumentRemark(): ?string
	{
		return $this->documentRemark;
	}

	public function getBillingAddress(): ?DocumentAddress
	{
		return $this->billingAddress;
	}

	public function getDeliveryAddress(): ?DocumentAddress
	{
		return $this->deliveryAddress;
	}

	public function getProformaInvoice(): ?ProformaInvoice
	{
		return $this->proformaInvoice;
	}

	public function setProformaInvoice(?ProformaInvoice $proformaInvoice): void
	{
		$this->proformaInvoice = $proformaInvoice;
	}

	public function getEet(): ?InvoiceEET
	{
		return $this->eet;
	}

	public function setEet(?InvoiceEET $eet): void
	{
		$this->eet = $eet;
	}

	public function __construct(Project $project)
	{
		parent::__construct($project);
		$this->actionLogs = new ArrayCollection();
	}
}
