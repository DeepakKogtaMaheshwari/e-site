<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private string $orderNumber;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = 'pending'; // pending, confirmed, processing, shipped, delivered, cancelled

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $subtotal = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $taxAmount = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $shippingAmount = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $discountAmount = '0.00';

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private string $totalAmount = '0.00';

    #[ORM\Column(type: 'string', length: 10)]
    private string $currency = 'INR';

    #[ORM\Column(type: 'string', length: 50)]
    private string $paymentMethod = 'cod'; // cod, online, wallet

    #[ORM\Column(type: 'string', length: 20)]
    private string $paymentStatus = 'pending'; // pending, paid, failed, refunded

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    // Shipping Address
    #[ORM\Column(type: 'string', length: 100)]
    private string $shippingFirstName;

    #[ORM\Column(type: 'string', length: 100)]
    private string $shippingLastName;

    #[ORM\Column(type: 'string', length: 15)]
    private string $shippingMobile;

    #[ORM\Column(type: 'text')]
    private string $shippingStreetAddress;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $shippingLandmark = null;

    #[ORM\Column(type: 'string', length: 100)]
    private string $shippingCity;

    #[ORM\Column(type: 'string', length: 100)]
    private string $shippingState;

    #[ORM\Column(type: 'string', length: 10)]
    private string $shippingPostalCode;

    #[ORM\Column(type: 'string', length: 100)]
    private string $shippingCountry = 'India';

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $shippedAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $deliveredAt = null;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->orderNumber = $this->generateOrderNumber();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        $this->updatedAt = new \DateTimeImmutable();
        return $this;
    }

    public function getSubtotal(): string
    {
        return $this->subtotal;
    }

    public function setSubtotal(string $subtotal): static
    {
        $this->subtotal = $subtotal;
        return $this;
    }

    public function getTaxAmount(): string
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(string $taxAmount): static
    {
        $this->taxAmount = $taxAmount;
        return $this;
    }

    public function getShippingAmount(): string
    {
        return $this->shippingAmount;
    }

    public function setShippingAmount(string $shippingAmount): static
    {
        $this->shippingAmount = $shippingAmount;
        return $this;
    }

    public function getDiscountAmount(): string
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(string $discountAmount): static
    {
        $this->discountAmount = $discountAmount;
        return $this;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;
        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): static
    {
        $this->currency = $currency;
        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    public function getPaymentStatus(): string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;
        return $this;
    }

    public function getShippingFirstName(): string
    {
        return $this->shippingFirstName;
    }

    public function setShippingFirstName(string $shippingFirstName): static
    {
        $this->shippingFirstName = $shippingFirstName;
        return $this;
    }

    public function getShippingLastName(): string
    {
        return $this->shippingLastName;
    }

    public function setShippingLastName(string $shippingLastName): static
    {
        $this->shippingLastName = $shippingLastName;
        return $this;
    }

    public function getShippingFullName(): string
    {
        return $this->shippingFirstName . ' ' . $this->shippingLastName;
    }

    public function getShippingMobile(): string
    {
        return $this->shippingMobile;
    }

    public function setShippingMobile(string $shippingMobile): static
    {
        $this->shippingMobile = $shippingMobile;
        return $this;
    }

    public function getShippingStreetAddress(): string
    {
        return $this->shippingStreetAddress;
    }

    public function setShippingStreetAddress(string $shippingStreetAddress): static
    {
        $this->shippingStreetAddress = $shippingStreetAddress;
        return $this;
    }

    public function getShippingLandmark(): ?string
    {
        return $this->shippingLandmark;
    }

    public function setShippingLandmark(?string $shippingLandmark): static
    {
        $this->shippingLandmark = $shippingLandmark;
        return $this;
    }

    public function getShippingCity(): string
    {
        return $this->shippingCity;
    }

    public function setShippingCity(string $shippingCity): static
    {
        $this->shippingCity = $shippingCity;
        return $this;
    }

    public function getShippingState(): string
    {
        return $this->shippingState;
    }

    public function setShippingState(string $shippingState): static
    {
        $this->shippingState = $shippingState;
        return $this;
    }

    public function getShippingPostalCode(): string
    {
        return $this->shippingPostalCode;
    }

    public function setShippingPostalCode(string $shippingPostalCode): static
    {
        $this->shippingPostalCode = $shippingPostalCode;
        return $this;
    }

    public function getShippingCountry(): string
    {
        return $this->shippingCountry;
    }

    public function setShippingCountry(string $shippingCountry): static
    {
        $this->shippingCountry = $shippingCountry;
        return $this;
    }

    public function getShippingAddress(): string
    {
        $address = $this->shippingStreetAddress;
        if ($this->shippingLandmark) {
            $address .= ', ' . $this->shippingLandmark;
        }
        $address .= ', ' . $this->shippingCity . ', ' . $this->shippingState . ' - ' . $this->shippingPostalCode;
        $address .= ', ' . $this->shippingCountry;
        
        return $address;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getShippedAt(): ?\DateTimeImmutable
    {
        return $this->shippedAt;
    }

    public function setShippedAt(?\DateTimeImmutable $shippedAt): static
    {
        $this->shippedAt = $shippedAt;
        return $this;
    }

    public function getDeliveredAt(): ?\DateTimeImmutable
    {
        return $this->deliveredAt;
    }

    public function setDeliveredAt(?\DateTimeImmutable $deliveredAt): static
    {
        $this->deliveredAt = $deliveredAt;
        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem)) {
            // set the owning side to null (unless already changed)
            if ($orderItem->getOrder() === $this) {
                $orderItem->setOrder(null);
            }
        }

        return $this;
    }

    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'Order Placed',
            'confirmed' => 'Confirmed',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function getStatusColor(): string
    {
        return match ($this->status) {
            'pending' => 'yellow',
            'confirmed' => 'blue',
            'processing' => 'purple',
            'shipped' => 'indigo',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    public function calculateTotals(): void
    {
        $subtotal = 0;
        foreach ($this->orderItems as $item) {
            $subtotal += (float) $item->getTotalPrice();
        }
        
        $this->subtotal = (string) $subtotal;
        
        // Calculate tax (18% GST)
        $this->taxAmount = (string) ($subtotal * 0.18);
        
        // Free shipping for orders above ₹500
        $this->shippingAmount = $subtotal >= 500 ? '0.00' : '50.00';
        
        // Calculate total
        $total = $subtotal + (float) $this->taxAmount + (float) $this->shippingAmount - (float) $this->discountAmount;
        $this->totalAmount = (string) $total;
    }

    private function generateOrderNumber(): string
    {
        return 'B2B' . date('Ymd') . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    public function copyShippingFromUser(User $user): void
    {
        $this->shippingFirstName = $user->getFirstName();
        $this->shippingLastName = $user->getLastName();
        $this->shippingMobile = $user->getMobile();
        $this->shippingStreetAddress = $user->getStreetAddress();
        $this->shippingLandmark = $user->getLandmark();
        $this->shippingCity = $user->getCity();
        $this->shippingState = $user->getState();
        $this->shippingPostalCode = $user->getPostalCode();
        $this->shippingCountry = $user->getCountry();
    }
}
