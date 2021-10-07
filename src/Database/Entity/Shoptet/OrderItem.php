<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\Attributes;
use App\Database\Repository\Shoptet\OrderItemRepository;

use Doctrine\ORM\Mapping as ORM;

#[Orm\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: 'sf_order_item')]
#[ORM\HasLifecycleCallbacks]
class OrderItem
{
	use Attributes\TId;

	#[ORM\ManyToOne(targetEntity: Order::class)]
	#[ORM\JoinColumn(name: 'document_id', nullable: false, onDelete: 'CASCADE')]
	protected Order $document;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $supplierName = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $amountCompleted = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $buyPriceWithVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $buyPriceWithoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $buyPriceVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $buyPriceVatRate = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $recyclingFeeCategory = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $recyclingFee = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $statusId = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $statusName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $mainImageName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $mainImageNeoName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $mainImageCdnName = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $mainImagePriority = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $mainImageDescription = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $stockLocation = null;

	#[ORM\Column(type: 'integer', nullable: false)]
	protected int $itemId;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $warrantyDescription = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $productGuid = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $code = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $itemType;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $name = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $variantName = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $brand = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $remark = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $weight = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $additionalField = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $amount = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $amountUnit = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $priceRatio = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $itemPriceWithVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $itemPriceWithoutVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $itemPriceVat = null;

	#[ORM\Column(type: 'integer', nullable: true)]
	protected ?int $itemPriceVatRate = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $controlHash;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $unitPriceWithVat = null;

	#[ORM\Column(type: 'float', nullable: true)]
	protected ?float $unitPriceWithoutVat = null;

	public function getDocument(): Order
	{
		return $this->document;
	}

	public function setDocument(Order $document): void
	{
		$this->document = $document;
	}

	public function getSupplierName(): ?string
	{
		return $this->supplierName;
	}

	public function setSupplierName(?string $supplierName): void
	{
		$this->supplierName = $supplierName;
	}

	public function getAmountCompleted(): ?float
	{
		return $this->amountCompleted;
	}

	public function setAmountCompleted(?float $amountCompleted): void
	{
		$this->amountCompleted = $amountCompleted;
	}

	public function getBuyPriceWithVat(): ?float
	{
		return $this->buyPriceWithVat;
	}

	public function setBuyPriceWithVat(?float $buyPriceWithVat): void
	{
		$this->buyPriceWithVat = $buyPriceWithVat;
	}

	public function getBuyPriceWithoutVat(): ?float
	{
		return $this->buyPriceWithoutVat;
	}

	public function setBuyPriceWithoutVat(?float $buyPriceWithoutVat): void
	{
		$this->buyPriceWithoutVat = $buyPriceWithoutVat;
	}

	public function getBuyPriceVat(): ?float
	{
		return $this->buyPriceVat;
	}

	public function setBuyPriceVat(?float $buyPriceVat): void
	{
		$this->buyPriceVat = $buyPriceVat;
	}

	public function getBuyPriceVatRate(): ?float
	{
		return $this->buyPriceVatRate;
	}

	public function setBuyPriceVatRate(?float $buyPriceVatRate): void
	{
		$this->buyPriceVatRate = $buyPriceVatRate;
	}

	public function getRecyclingFeeCategory(): ?string
	{
		return $this->recyclingFeeCategory;
	}

	public function setRecyclingFeeCategory(?string $recyclingFeeCategory): void
	{
		$this->recyclingFeeCategory = $recyclingFeeCategory;
	}

	public function getRecyclingFee(): ?string
	{
		return $this->recyclingFee;
	}

	public function setRecyclingFee(?string $recyclingFee): void
	{
		$this->recyclingFee = $recyclingFee;
	}

	public function getStatusId(): ?int
	{
		return $this->statusId;
	}

	public function setStatusId(?int $statusId): void
	{
		$this->statusId = $statusId;
	}

	public function getStatusName(): ?string
	{
		return $this->statusName;
	}

	public function setStatusName(?string $statusName): void
	{
		$this->statusName = $statusName;
	}

	public function getMainImageName(): ?string
	{
		return $this->mainImageName;
	}

	public function setMainImageName(?string $mainImageName): void
	{
		$this->mainImageName = $mainImageName;
	}

	public function getMainImageNeoName(): ?string
	{
		return $this->mainImageNeoName;
	}

	public function setMainImageNeoName(?string $mainImageNeoName): void
	{
		$this->mainImageNeoName = $mainImageNeoName;
	}

	public function getMainImageCdnName(): ?string
	{
		return $this->mainImageCdnName;
	}

	public function setMainImageCdnName(?string $mainImageCdnName): void
	{
		$this->mainImageCdnName = $mainImageCdnName;
	}

	public function getMainImagePriority(): ?int
	{
		return $this->mainImagePriority;
	}

	public function setMainImagePriority(?int $mainImagePriority): void
	{
		$this->mainImagePriority = $mainImagePriority;
	}

	public function getMainImageDescription(): ?string
	{
		return $this->mainImageDescription;
	}

	public function setMainImageDescription(?string $mainImageDescription): void
	{
		$this->mainImageDescription = $mainImageDescription;
	}

	public function getStockLocation(): ?string
	{
		return $this->stockLocation;
	}

	public function setStockLocation(?string $stockLocation): void
	{
		$this->stockLocation = $stockLocation;
	}

	public function getItemId(): int
	{
		return $this->itemId;
	}

	public function setItemId(int $itemId): void
	{
		$this->itemId = $itemId;
	}

	public function getWarrantyDescription(): ?string
	{
		return $this->warrantyDescription;
	}

	public function setWarrantyDescription(?string $warrantyDescription): void
	{
		$this->warrantyDescription = $warrantyDescription;
	}

	public function getProductGuid(): ?string
	{
		return $this->productGuid;
	}

	public function setProductGuid(?string $productGuid): void
	{
		$this->productGuid = $productGuid;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function setCode(?string $code): void
	{
		$this->code = $code;
	}

	public function getItemType(): string
	{
		return $this->itemType;
	}

	public function setItemType(string $itemType): void
	{
		$this->itemType = $itemType;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	public function getVariantName(): ?string
	{
		return $this->variantName;
	}

	public function setVariantName(?string $variantName): void
	{
		$this->variantName = $variantName;
	}

	public function getBrand(): ?string
	{
		return $this->brand;
	}

	public function setBrand(?string $brand): void
	{
		$this->brand = $brand;
	}

	public function getRemark(): ?string
	{
		return $this->remark;
	}

	public function setRemark(?string $remark): void
	{
		$this->remark = $remark;
	}

	public function getWeight(): ?float
	{
		return $this->weight;
	}

	public function setWeight(?float $weight): void
	{
		$this->weight = $weight;
	}

	public function getAdditionalField(): ?string
	{
		return $this->additionalField;
	}

	public function setAdditionalField(?string $additionalField): void
	{
		$this->additionalField = $additionalField;
	}

	public function getAmount(): ?float
	{
		return $this->amount;
	}

	public function setAmount(?float $amount): void
	{
		$this->amount = $amount;
	}

	public function getAmountUnit(): ?string
	{
		return $this->amountUnit;
	}

	public function setAmountUnit(?string $amountUnit): void
	{
		$this->amountUnit = $amountUnit;
	}

	public function getPriceRatio(): ?float
	{
		return $this->priceRatio;
	}

	public function setPriceRatio(?float $priceRatio): void
	{
		$this->priceRatio = $priceRatio;
	}

	public function getItemPriceWithVat(): ?float
	{
		return $this->itemPriceWithVat;
	}

	public function setItemPriceWithVat(?float $itemPriceWithVat): void
	{
		$this->itemPriceWithVat = $itemPriceWithVat;
	}

	public function getItemPriceWithoutVat(): ?float
	{
		return $this->itemPriceWithoutVat;
	}

	public function setItemPriceWithoutVat(?float $itemPriceWithoutVat): void
	{
		$this->itemPriceWithoutVat = $itemPriceWithoutVat;
	}

	public function getItemPriceVat(): ?float
	{
		return $this->itemPriceVat;
	}

	public function setItemPriceVat(?float $itemPriceVat): void
	{
		$this->itemPriceVat = $itemPriceVat;
	}

	public function getItemPriceVatRate(): ?int
	{
		return $this->itemPriceVatRate;
	}

	public function setItemPriceVatRate(?int $itemPriceVatRate): void
	{
		$this->itemPriceVatRate = $itemPriceVatRate;
	}

	public function getControlHash(): string
	{
		return $this->controlHash;
	}

	public function setControlHash(string $controlHash): void
	{
		$this->controlHash = $controlHash;
	}

	public function getUnitPriceWithVat(): ?float
	{
		return $this->unitPriceWithVat;
	}

	public function setUnitPriceWithVat(?float $unitPriceWithVat): void
	{
		$this->unitPriceWithVat = $unitPriceWithVat;
	}

	public function getUnitPriceWithoutVat(): ?float
	{
		return $this->unitPriceWithoutVat;
	}

	public function setUnitPriceWithoutVat(?float $unitPriceWithoutVat): void
	{
		$this->unitPriceWithoutVat = $unitPriceWithoutVat;
	}
}
