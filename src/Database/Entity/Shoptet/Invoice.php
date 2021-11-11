<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
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

	#[ORM\ManyToOne(targetEntity: ProformaInvoice::class)]
	#[ORM\JoinColumn(name: 'proforma_invoice_id', nullable: true, onDelete: 'SET NULL')]
	protected ?ProformaInvoice $proformaInvoice = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $taxDate = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $documentRemark = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: InvoiceBillingAddress::class)]
	protected ?DocumentAddress $billingAddress = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: InvoiceDeliveryAddress::class)]
	protected ?DocumentAddress $deliveryAddress = null;

	/** @var ArrayCollection<int, InvoiceItem>|Collection<int, InvoiceItem> */
	#[ORM\OneToMany(mappedBy: 'document', targetEntity: InvoiceItem::class)]
	protected Collection|ArrayCollection $items;

	#[ORM\OneToOne(mappedBy: 'invoice', targetEntity: InvoiceEET::class)]
	protected ?InvoiceEET $eet = null;

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
}
