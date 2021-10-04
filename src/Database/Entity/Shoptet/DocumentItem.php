<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use Doctrine\ORM\Mapping as ORM;

#[ORM\MappedSuperclass]
abstract class DocumentItem
{
	protected Document $document;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $productGuid = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $itemType;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $code = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $name = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $variantName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $brand = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $amount = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $amountUnit = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $weight = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $remark = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $priceRatio = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $additionalField = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $withVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $withoutVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $unitWithVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $unitWithoutVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $vat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $vatRate = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $controlHash;

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

	public function setAmount(?string $amount): void
	{
		$this->amount = $amount;
	}

	public function setAmountUnit(?string $amountUnit): void
	{
		$this->amountUnit = $amountUnit;
	}

	public function setWeight(?string $weight): void
	{
		$this->weight = $weight;
	}

	public function setRemark(?string $remark): void
	{
		$this->remark = $remark;
	}

	public function setPriceRatio(?string $priceRatio): void
	{
		$this->priceRatio = $priceRatio;
	}

	public function setAdditionalField(?string $additionalField): void
	{
		$this->additionalField = $additionalField;
	}

	public function setWithVat(?string $withVat): void
	{
		$this->withVat = $withVat;
	}

	public function setWithoutVat(?string $withoutVat): void
	{
		$this->withoutVat = $withoutVat;
	}

	public function setVat(?string $vat): void
	{
		$this->vat = $vat;
	}

	public function setVatRate(?string $vatRate): void
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

	public function getAmount(): ?string
	{
		return $this->amount;
	}

	public function getAmountUnit(): ?string
	{
		return $this->amountUnit;
	}

	public function getWeight(): ?string
	{
		return $this->weight;
	}

	public function getRemark(): ?string
	{
		return $this->remark;
	}

	public function getPriceRatio(): ?string
	{
		return $this->priceRatio;
	}

	public function getAdditionalField(): ?string
	{
		return $this->additionalField;
	}

	public function getWithVat(): ?string
	{
		return $this->withVat;
	}

	public function getWithoutVat(): ?string
	{
		return $this->withoutVat;
	}

	public function getVat(): ?string
	{
		return $this->vat;
	}

	public function getVatRate(): ?string
	{
		return $this->vatRate;
	}

	public function getControlHash(): string
	{
		return $this->controlHash;
	}

	public function getUnitWithVat(): ?string
	{
		return $this->unitWithVat;
	}

	public function setUnitWithVat(?string $unitWithVat): void
	{
		$this->unitWithVat = $unitWithVat;
	}

	public function getUnitWithoutVat(): ?string
	{
		return $this->unitWithoutVat;
	}

	public function setUnitWithoutVat(?string $unitWithoutVat): void
	{
		$this->unitWithoutVat = $unitWithoutVat;
	}

	//todo displayPrices
}
