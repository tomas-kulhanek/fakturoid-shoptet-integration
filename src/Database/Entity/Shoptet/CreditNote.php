<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\CreditNoteRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: CreditNoteRepository::class)]
#[ORM\Table(name: 'sf_credit_note')]
#[ORM\HasLifecycleCallbacks]
class CreditNote extends Document
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Invoice::class)]
	#[ORM\JoinColumn(name: 'invoice_id', nullable: true, onDelete: 'SET NULL')]
	protected ?Invoice $invoice = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: CreditNoteBillingAddress::class)]
	protected ?DocumentAddress $billingAddress = null;

	#[ORM\OneToOne(mappedBy: 'document', targetEntity: CreditNoteDeliveryAddress::class)]
	protected ?DocumentAddress $deliveryAddress = null;

	/** @var ArrayCollection<int, CreditNoteItem|DocumentItem>|Collection<int, CreditNoteItem|DocumentItem> */
	#[ORM\OneToMany(mappedBy: 'document', targetEntity: CreditNoteItem::class)]
	protected Collection|ArrayCollection $items;

	#[ORM\Column(type: 'datetime_immutable', nullable: true)]
	protected ?DateTimeImmutable $taxDate = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $documentRemark = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $stockAmountChangeType = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $invoiceCode;


	public function setTaxDate(?DateTimeImmutable $taxDate): void
	{
		$this->taxDate = $taxDate;
	}

	public function setDocumentRemark(?string $documentRemark): void
	{
		$this->documentRemark = $documentRemark;
	}

	public function setStockAmountChangeType(?string $stockAmountChangeType): void
	{
		$this->stockAmountChangeType = $stockAmountChangeType;
	}

	public function setInvoiceCode(string $invoiceCode): void
	{
		$this->invoiceCode = $invoiceCode;
	}

	public function getTaxDate(): ?DateTimeImmutable
	{
		return $this->taxDate;
	}

	public function getDocumentRemark(): ?string
	{
		return $this->documentRemark;
	}

	public function getStockAmountChangeType(): ?string
	{
		return $this->stockAmountChangeType;
	}

	public function getInvoiceCode(): string
	{
		return $this->invoiceCode;
	}

	public function getInvoice(): ?Invoice
	{
		return $this->invoice;
	}

	public function setInvoice(?Invoice $invoice): void
	{
		$this->invoice = $invoice;
	}

	//protected ?Customer $customer = null;
	//protected ?EetReceipt $eetReceipt = null;
}
