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

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $amountCompleted = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $buyPriceWithVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $buyPriceWithoutVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $buyPriceVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $buyPriceVatRate = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $recyclingFeeCategory = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $recyclingFee = null;

	#[ORM\Column(type: 'string', nullable: true)]
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

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $weight = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $additionalField = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $amount = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $amountUnit = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $priceRatio = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $itemPriceWithVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $itemPriceWithoutVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $itemPriceVat = null;

	#[ORM\Column(type: 'string', nullable: true)]
	protected ?string $itemPriceVatRate = null;

	#[ORM\Column(type: 'string', nullable: false)]
	protected string $controlHash;

	public function setDocument(Order $document): void
	{
		$this->document = $document;
	}

	public function setSupplierName(?string $supplierName): void
	{
		$this->supplierName = $supplierName;
	}

	public function setAmountCompleted(?string $amountCompleted): void
	{
		$this->amountCompleted = $amountCompleted;
	}

	public function setBuyPriceWithVat(?string $buyPriceWithVat): void
	{
		$this->buyPriceWithVat = $buyPriceWithVat;
	}

	public function setBuyPriceWithoutVat(?string $buyPriceWithoutVat): void
	{
		$this->buyPriceWithoutVat = $buyPriceWithoutVat;
	}

	public function setBuyPriceVat(?string $buyPriceVat): void
	{
		$this->buyPriceVat = $buyPriceVat;
	}

	public function setBuyPriceVatRate(?string $buyPriceVatRate): void
	{
		$this->buyPriceVatRate = $buyPriceVatRate;
	}

	public function setRecyclingFeeCategory(?string $recyclingFeeCategory): void
	{
		$this->recyclingFeeCategory = $recyclingFeeCategory;
	}

	public function setRecyclingFee(?string $recyclingFee): void
	{
		$this->recyclingFee = $recyclingFee;
	}

	public function setStatusId(?int $statusId): void
	{
		$this->statusId = $statusId;
	}

	public function setStatusName(?string $statusName): void
	{
		$this->statusName = $statusName;
	}

	public function setMainImageName(?string $mainImageName): void
	{
		$this->mainImageName = $mainImageName;
	}

	public function setMainImageNeoName(?string $mainImageNeoName): void
	{
		$this->mainImageNeoName = $mainImageNeoName;
	}

	public function setMainImageCdnName(?string $mainImageCdnName): void
	{
		$this->mainImageCdnName = $mainImageCdnName;
	}

	public function setMainImagePriority(?int $mainImagePriority): void
	{
		$this->mainImagePriority = $mainImagePriority;
	}

	public function setMainImageDescription(?string $mainImageDescription): void
	{
		$this->mainImageDescription = $mainImageDescription;
	}

	public function setStockLocation(?string $stockLocation): void
	{
		$this->stockLocation = $stockLocation;
	}

	public function setItemId(int $itemId): void
	{
		$this->itemId = $itemId;
	}

	public function setWarrantyDescription(?string $warrantyDescription): void
	{
		$this->warrantyDescription = $warrantyDescription;
	}

	public function setProductGuid(?string $productGuid): void
	{
		$this->productGuid = $productGuid;
	}

	public function setCode(?string $code): void
	{
		$this->code = $code;
	}

	public function setItemType(string $itemType): void
	{
		$this->itemType = $itemType;
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

	public function setRemark(?string $remark): void
	{
		$this->remark = $remark;
	}

	public function setWeight(?string $weight): void
	{
		$this->weight = $weight;
	}

	public function setAdditionalField(?string $additionalField): void
	{
		$this->additionalField = $additionalField;
	}

	public function setAmount(?string $amount): void
	{
		$this->amount = $amount;
	}

	public function setAmountUnit(?string $amountUnit): void
	{
		$this->amountUnit = $amountUnit;
	}

	public function setPriceRatio(?string $priceRatio): void
	{
		$this->priceRatio = $priceRatio;
	}

	public function setItemPriceWithVat(?string $itemPriceWithVat): void
	{
		$this->itemPriceWithVat = $itemPriceWithVat;
	}

	public function setItemPriceWithoutVat(?string $itemPriceWithoutVat): void
	{
		$this->itemPriceWithoutVat = $itemPriceWithoutVat;
	}

	public function setItemPriceVat(?string $itemPriceVat): void
	{
		$this->itemPriceVat = $itemPriceVat;
	}

	public function setItemPriceVatRate(?string $itemPriceVatRate): void
	{
		$this->itemPriceVatRate = $itemPriceVatRate;
	}

	public function getDocument(): Order
	{
		return $this->document;
	}

	public function getSupplierName(): ?string
	{
		return $this->supplierName;
	}

	public function getAmountCompleted(): ?string
	{
		return $this->amountCompleted;
	}

	public function getBuyPriceWithVat(): ?string
	{
		return $this->buyPriceWithVat;
	}

	public function getBuyPriceWithoutVat(): ?string
	{
		return $this->buyPriceWithoutVat;
	}

	public function getBuyPriceVat(): ?string
	{
		return $this->buyPriceVat;
	}

	public function getBuyPriceVatRate(): ?string
	{
		return $this->buyPriceVatRate;
	}

	public function getRecyclingFeeCategory(): ?string
	{
		return $this->recyclingFeeCategory;
	}

	public function getRecyclingFee(): ?string
	{
		return $this->recyclingFee;
	}

	public function getStatusId(): ?int
	{
		return $this->statusId;
	}

	public function getStatusName(): ?string
	{
		return $this->statusName;
	}

	public function getMainImageName(): ?string
	{
		return $this->mainImageName;
	}

	public function getMainImageNeoName(): ?string
	{
		return $this->mainImageNeoName;
	}

	public function getMainImageCdnName(): ?string
	{
		return $this->mainImageCdnName;
	}

	public function getMainImagePriority(): ?int
	{
		return $this->mainImagePriority;
	}

	public function getMainImageDescription(): ?string
	{
		return $this->mainImageDescription;
	}

	public function getStockLocation(): ?string
	{
		return $this->stockLocation;
	}

	public function getItemId(): int
	{
		return $this->itemId;
	}

	public function getWarrantyDescription(): ?string
	{
		return $this->warrantyDescription;
	}

	public function getProductGuid(): ?string
	{
		return $this->productGuid;
	}

	public function getCode(): ?string
	{
		return $this->code;
	}

	public function getItemType(): string
	{
		return $this->itemType;
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

	public function getRemark(): ?string
	{
		return $this->remark;
	}

	public function getWeight(): ?string
	{
		return $this->weight;
	}

	public function getAdditionalField(): ?string
	{
		return $this->additionalField;
	}

	public function getAmount(): ?string
	{
		return $this->amount;
	}

	public function getAmountUnit(): ?string
	{
		return $this->amountUnit;
	}

	public function getPriceRatio(): ?string
	{
		return $this->priceRatio;
	}

	public function getItemPriceWithVat(): ?string
	{
		return $this->itemPriceWithVat;
	}

	public function getItemPriceWithoutVat(): ?string
	{
		return $this->itemPriceWithoutVat;
	}

	public function getItemPriceVat(): ?string
	{
		return $this->itemPriceVat;
	}

	public function getItemPriceVatRate(): ?string
	{
		return $this->itemPriceVatRate;
	}

	/* * @var DocumentPrice[]|null */
	//protected ?array $displayPrices = []; //todo


	public function setControlHash(string $controlHash): void
	{
		$this->controlHash = $controlHash;
	}

	public function getControlHash(): string
	{
		return $this->controlHash;
	}
}
