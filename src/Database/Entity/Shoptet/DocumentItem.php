<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class DocumentItem
{
	protected Document $document;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $productGuid = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $itemType;

	#[ORM\Column(name:'`code`', type: 'string', nullable: true)]
	protected ?string $code = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $name = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $variantName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $brand = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $amount = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $amountUnit = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $weight = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $remark = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceRatio = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $additionalField = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $withVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $withoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $unitWithVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $unitWithoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $vat = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $vatRate = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $controlHash;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $accountingId = null;

	#[ORM\Column(type: 'date_immutable', nullable: true)]
	protected ?DateTimeImmutable $deletedAt = null;

	public function setDocument(Document $document): void
	{
		$this->document = $document;
	}

	public function setProductGuid(?string $productGuid): void
	{
		$this->productGuid = $productGuid;
	}

	public function setItemType(string $itemType): void
	{
		$this->itemType = $itemType;
	}

	public function setCode(?string $code): void
	{
		$this->code = $code;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function setVariantName(?string $variantName): void
	{
		$this->variantName = $variantName;
	}

	public function setBrand(?string $brand): void
	{
		$this->brand = $brand;
	}

	public function setAmount(?float $amount): void
	{
		$this->amount = $amount;
	}

	public function setAmountUnit(?string $amountUnit): void
	{
		$this->amountUnit = $amountUnit;
	}

	public function setWeight(?float $weight): void
	{
		$this->weight = $weight;
	}

	public function setRemark(?string $remark): void
	{
		$this->remark = $remark;
	}

	public function setPriceRatio(?float $priceRatio): void
	{
		$this->priceRatio = $priceRatio;
	}

	public function setAdditionalField(?string $additionalField): void
	{
		$this->additionalField = $additionalField;
	}

	public function setWithVat(?float $withVat): void
	{
		$this->withVat = $withVat;
	}

	public function setWithoutVat(?float $withoutVat): void
	{
		$this->withoutVat = $withoutVat;
	}

	public function setVat(?float $vat): void
	{
		$this->vat = $vat;
	}

	public function setVatRate(?int $vatRate): void
	{
		$this->vatRate = $vatRate;
	}

	public function setControlHash(string $controlHash): void
	{
		$this->controlHash = $controlHash;
	}

	public function getDocument(): Document
	{
		return $this->document;
	}

	public function getProductGuid(): ?string
	{
		return $this->productGuid;
	}

	public function getItemType(): string
	{
		return $this->itemType;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function getVariantName(): ?string
	{
		return $this->variantName;
	}

	public function getBrand(): ?string
	{
		return $this->brand;
	}

	public function getAmount(): ?float
	{
		return $this->amount;
	}

	public function getAmountUnit(): ?string
	{
		return $this->amountUnit;
	}

	public function getWeight(): ?float
	{
		return $this->weight;
	}

	public function getRemark(): ?string
	{
		return $this->remark;
	}

	public function getPriceRatio(): ?float
	{
		return $this->priceRatio;
	}

	public function getAdditionalField(): ?string
	{
		return $this->additionalField;
	}

	public function getWithVat(): ?float
	{
		return $this->withVat;
	}

	public function getWithoutVat(): ?float
	{
		return $this->withoutVat;
	}

	public function getVat(): ?float
	{
		return $this->vat;
	}

	public function getVatRate(): ?int
	{
		return $this->vatRate;
	}

	public function getControlHash(): string
	{
		return $this->controlHash;
	}

	public function getUnitWithVat(): ?float
	{
		return $this->unitWithVat;
	}

	public function setUnitWithVat(?float $unitWithVat): void
	{
		$this->unitWithVat = $unitWithVat;
	}

	public function getUnitWithoutVat(): ?float
	{
		return $this->unitWithoutVat;
	}

	public function setUnitWithoutVat(?float $unitWithoutVat): void
	{
		$this->unitWithoutVat = $unitWithoutVat;
	}

	public function getAccountingId(): ?int
	{
		return $this->accountingId;
	}

	public function setAccountingId(?int $accountingId): void
	{
		$this->accountingId = $accountingId;
	}

	public function getDeletedAt(): ?DateTimeImmutable
	{
		return $this->deletedAt;
	}

	public function setDeletedAt(?DateTimeImmutable $deletedAt): void
	{
		$this->deletedAt = $deletedAt;
	}

	//todo displayPrices
}
