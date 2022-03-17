<?php

declare(strict_types=1);


namespace App\Database\Entity\Shoptet;

use App\Database\Entity\AbstractEntity;
use App\Database\Entity\Accounting\BankAccount;
use App\Database\Entity\Attributes\TId;
use App\Database\Repository\Shoptet\CurrencyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CurrencyRepository::class)]
#[ORM\Table(name: 'sf_currency')]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'project_code_cashdesk', fields: ['project', 'code', 'cashdesk'])]
class Currency extends AbstractEntity
{
	use TId;

	#[ORM\ManyToOne(targetEntity: Project::class)]
	#[ORM\JoinColumn(name: 'project_id', nullable: false, onDelete: 'CASCADE')]
	private Project $project;

	#[ORM\ManyToOne(targetEntity: BankAccount::class)]
	#[ORM\JoinColumn(name: 'bank_account_id', nullable: true, onDelete: 'SET NULL')]
	private ?BankAccount $bankAccount = null;

	#[ORM\Column(name: '`code`', type: 'string', nullable: false)]
	private string $code;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $title;

	#[ORM\Column(type: 'string', nullable: false)]
	private string $rounding = 'up';

	#[ORM\Column(type: 'boolean', nullable: false)]
	private bool $isDefault = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	private bool $isDefaultAdmin = false;

	#[ORM\Column(type: 'boolean', nullable: false)]
	private bool $isVisible = false;

	#[ORM\Column(type: 'integer', nullable: false)]
	private int $priority;

	#[ORM\Column(type: 'integer', nullable: false)]
	private int $priceDecimalPlaces = 2;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	private bool $cashdesk = false;

	#[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
	protected bool $accountingRoundTotal = false;

	public function __construct(Project $project)
	{
		$this->project = $project;
	}

	public function getProject(): Project
	{
		return $this->project;
	}

	public function getBankAccount(): ?BankAccount
	{
		return $this->bankAccount;
	}

	public function setBankAccount(?BankAccount $bankAccount): void
	{
		$this->bankAccount = $bankAccount;
	}

	public function getCode(): string
	{
		return $this->code;
	}

	public function setCode(string $code): void
	{
		$this->code = $code;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): void
	{
		$this->title = $title;
	}

	public function isDefault(): bool
	{
		return $this->isDefault;
	}

	public function setIsDefault(bool $isDefault): void
	{
		$this->isDefault = $isDefault;
	}

	public function isDefaultAdmin(): bool
	{
		return $this->isDefaultAdmin;
	}

	public function setIsDefaultAdmin(bool $isDefaultAdmin): void
	{
		$this->isDefaultAdmin = $isDefaultAdmin;
	}

	public function isVisible(): bool
	{
		return $this->isVisible;
	}

	public function setIsVisible(bool $isVisible): void
	{
		$this->isVisible = $isVisible;
	}

	public function getPriority(): int
	{
		return $this->priority;
	}

	public function setPriority(int $priority): void
	{
		$this->priority = $priority;
	}

	public function getPriceDecimalPlaces(): int
	{
		return $this->priceDecimalPlaces;
	}

	public function setPriceDecimalPlaces(int $priceDecimalPlaces): void
	{
		$this->priceDecimalPlaces = $priceDecimalPlaces;
	}

	public function isCashdesk(): bool
	{
		return $this->cashdesk;
	}

	public function setCashdesk(bool $cashdesk): void
	{
		$this->cashdesk = $cashdesk;
	}

	public function getRounding(): string
	{
		return $this->rounding;
	}

	public function setRounding(string $rounding): void
	{
		$this->rounding = $rounding;
	}

	public function getAccountingRoundTotal(): int
	{
		return (int)$this->accountingRoundTotal;
	}

	public function isAccountingRoundTotal(): bool
	{
		return $this->accountingRoundTotal;
	}

	public function setAccountingRoundTotal(bool $accountingRoundTotal): void
	{
		$this->accountingRoundTotal = $accountingRoundTotal;
	}
}
