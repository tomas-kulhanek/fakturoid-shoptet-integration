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


	#[ORM\ManyToOne(targetEntity: Order::class)]
	#[ORM\JoinColumn(name: 'order_id', nullable: true, onDelete: 'SET NULL')]
	protected ?Order $order = null;

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


	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $fakturoidNumber = null;
	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidIssuedAt = null;
	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $fakturoidId = null;
	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $fakturoidSubjectId = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidSentAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidPaidAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidReminderSentAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidAcceptedAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidCancelledAt = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $fakturoidWebinvoiceSeenAt = null;

	#[Orm\Column(type: 'string', nullable: true)]
	protected ?string $fakturoidPublicToken = null;

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

	public function getOrder(): ?Order
	{
		return $this->order;
	}

	public function setOrder(?Order $order): void
	{
		$this->order = $order;
	}

	public function getProformaInvoice(): ?ProformaInvoice
	{
		return $this->proformaInvoice;
	}

	public function setProformaInvoice(?ProformaInvoice $proformaInvoice): void
	{
		$this->proformaInvoice = $proformaInvoice;
	}

	public function getFakturoidNumber(): ?string
	{
		return $this->fakturoidNumber;
	}

	public function setFakturoidNumber(?string $fakturoidNumber): void
	{
		$this->fakturoidNumber = $fakturoidNumber;
	}

	public function getFakturoidIssuedAt(): ?DateTimeImmutable
	{
		return $this->fakturoidIssuedAt;
	}

	public function setFakturoidIssuedAt(?DateTimeImmutable $fakturoidIssuedAt): void
	{
		$this->fakturoidIssuedAt = $fakturoidIssuedAt;
	}

	public function getFakturoidId(): ?int
	{
		return $this->fakturoidId;
	}

	public function setFakturoidId(?int $fakturoidId): void
	{
		$this->fakturoidId = $fakturoidId;
	}

	public function getFakturoidSubjectId(): ?int
	{
		return $this->fakturoidSubjectId;
	}

	public function setFakturoidSubjectId(?int $fakturoidSubjectId): void
	{
		$this->fakturoidSubjectId = $fakturoidSubjectId;
	}

	public function getFakturoidSentAt(): ?DateTimeImmutable
	{
		return $this->fakturoidSentAt;
	}

	public function setFakturoidSentAt(?DateTimeImmutable $fakturoidSentAt): void
	{
		$this->fakturoidSentAt = $fakturoidSentAt;
	}

	public function getFakturoidPaidAt(): ?DateTimeImmutable
	{
		return $this->fakturoidPaidAt;
	}

	public function setFakturoidPaidAt(?DateTimeImmutable $fakturoidPaidAt): void
	{
		$this->fakturoidPaidAt = $fakturoidPaidAt;
	}

	public function getFakturoidReminderSentAt(): ?DateTimeImmutable
	{
		return $this->fakturoidReminderSentAt;
	}

	public function setFakturoidReminderSentAt(?DateTimeImmutable $fakturoidReminderSentAt): void
	{
		$this->fakturoidReminderSentAt = $fakturoidReminderSentAt;
	}

	public function getFakturoidAcceptedAt(): ?DateTimeImmutable
	{
		return $this->fakturoidAcceptedAt;
	}

	public function setFakturoidAcceptedAt(?DateTimeImmutable $fakturoidAcceptedAt): void
	{
		$this->fakturoidAcceptedAt = $fakturoidAcceptedAt;
	}

	public function getFakturoidCancelledAt(): ?DateTimeImmutable
	{
		return $this->fakturoidCancelledAt;
	}

	public function setFakturoidCancelledAt(?DateTimeImmutable $fakturoidCancelledAt): void
	{
		$this->fakturoidCancelledAt = $fakturoidCancelledAt;
	}

	public function getFakturoidWebinvoiceSeenAt(): ?DateTimeImmutable
	{
		return $this->fakturoidWebinvoiceSeenAt;
	}

	public function setFakturoidWebinvoiceSeenAt(?DateTimeImmutable $fakturoidWebinvoiceSeenAt): void
	{
		$this->fakturoidWebinvoiceSeenAt = $fakturoidWebinvoiceSeenAt;
	}

	public function getFakturoidPublicToken(): ?string
	{
		return $this->fakturoidPublicToken;
	}

	public function setFakturoidPublicToken(?string $fakturoidPublicToken): void
	{
		$this->fakturoidPublicToken = $fakturoidPublicToken;
	}


	//protected ?Customer $customer = null;
	//protected ?EetReceipt $eetReceipt = null;
}
