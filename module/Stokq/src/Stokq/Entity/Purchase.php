<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="purchases")
 * @ORM\HasLifecycleCallbacks
 */
class Purchase implements IdProviderInterface
{
    const ALIAS = 'p';

    const STATUS_PLANNED = 'planned';
    const STATUS_IN_PROGRESS = 'in progress';
    const STATUS_CANCELED = 'canceled';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_RETURNED = 'returned';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", nullable=true, onDelete="SET NULL")
     */
    private $creator;

    /**
     * @var Supplier
     * @ORM\ManyToOne(targetEntity="Supplier", inversedBy="purchases", cascade={"persist"})
     * @ORM\JoinColumn(name="supplier_id", nullable=true, onDelete="SET NULL")
     */
    private $supplier;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title = '';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $po_number;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $currency = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $note = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $status = self::STATUS_PLANNED;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $ordered_at;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $delivered_at;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $canceled_at;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="update")
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_update;

    /**
     * @var ArrayCollection|PurchaseItem[]
     * @ORM\OneToMany(targetEntity="PurchaseItem", mappedBy="purchase", cascade={"persist", "remove"})
     */
    private $items;

    /**
     * @var WarehousePurchase
     * @ORM\OneToOne(targetEntity="WarehousePurchase", mappedBy="purchase", cascade={"persist", "remove"})
     */
    private $warehouse_purchase;

    /**
     * @param $status
     * @return string
     */
    public static function statusToLocaleID($status)
    {
        switch ($status) {
            case Purchase::STATUS_PLANNED:
                return 'direncanakan';
                break;

            case Purchase::STATUS_IN_PROGRESS:
                return 'diproses';
                break;

            case Purchase::STATUS_DELIVERED:
                return 'diterima';
                break;

            case Purchase::STATUS_CANCELED:
                return 'dibatalkan';
                break;

            case Purchase::STATUS_RETURNED:
                return 'dikembalikan';
                break;

            default:
                return '-';
                break;
        }
    }

    /**
     *
     */
    function __construct()
    {
        $this->items = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->updateDates();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->updateDates();
    }

    /**
     * @return Supplier
     */
    public function getSupplier()
    {
        return $this->supplier;
    }

    /**
     * @param Supplier $supplier
     */
    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
    }

    /**
     * @return string
     */
    public function getPoNumber()
    {
        return $this->po_number;
    }

    /**
     * @param string $po_number
     */
    public function setPoNumber($po_number)
    {
        $this->po_number = $po_number;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->last_update;
    }

    /**
     * @param \DateTime $last_update
     */
    public function setLastUpdate($last_update)
    {
        $this->last_update = $last_update;
    }

    /**
     * @return \DateTime
     */
    public function getCanceledAt()
    {
        return $this->canceled_at;
    }

    /**
     * @param \DateTime $canceled_at
     */
    public function setCanceledAt($canceled_at)
    {
        $this->canceled_at = $canceled_at;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param User $creator
     */
    public function setCreator($creator)
    {
        $this->creator = $creator;
    }

    /**
     * @return \DateTime
     */
    public function getDeliveredAt()
    {
        return $this->delivered_at;
    }

    /**
     * @param \DateTime $delivered_at
     */
    public function setDeliveredAt($delivered_at)
    {
        $this->delivered_at = $delivered_at;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return ArrayCollection|PurchaseItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection|PurchaseItem[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return \DateTime
     */
    public function getOrderedAt()
    {
        return $this->ordered_at;
    }

    /**
     * @param \DateTime $ordered_at
     */
    public function setOrderedAt($ordered_at)
    {
        $this->ordered_at = $ordered_at;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return WarehousePurchase
     */
    public function getWarehousePurchase()
    {
        return $this->warehouse_purchase;
    }

    /**
     * @param WarehousePurchase $warehouse_purchase
     */
    public function setWarehousePurchase($warehouse_purchase)
    {
        $this->warehouse_purchase = $warehouse_purchase;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Helper method to enable bind from input filter
     *
     * @param Warehouse $warehouse
     */
    public function setWarehouse(Warehouse $warehouse)
    {
        $warehousePurchase = new WarehousePurchase();
        $warehousePurchase->setWarehouse($warehouse);
        $warehousePurchase->setPurchase($this);
        $this->setWarehousePurchase($warehousePurchase);
    }

    /**
     * @return array
     */
    public function getItemsAsArray()
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            $items[$item->getItemName()] = [
                'item_name' => $item->getItemName(),
                'unit_price' => $item->getUnitPrice(),
                'quantity' => $item->getQuantity(),
                'subtotal' => $item->getSubtotal(),
                'total' => $item->getTotal(),
                'unit' => $item->getUnit(),
                'stock' => $item->getStockPurchase() ? $item->getStockPurchase()->getStock()->getId() : null
            ];
        }
        return $items;
    }

    /**
     * Update ordered_at, delivered_at, canceled_at when status changes.
     */
    protected function updateDates()
    {
        switch ($this->status) {
            case self::STATUS_DELIVERED:
                if (is_null($this->getOrderedAt()) || (!$this->getOrderedAt() instanceof \DateTime)) {
                    $this->setOrderedAt(new \DateTime());
                }
                if (is_null($this->getDeliveredAt()) || (!$this->getDeliveredAt() instanceof \DateTime)) {
                    $this->setDeliveredAt(new \DateTime());
                }
                break;

            case self::STATUS_IN_PROGRESS:
                if (is_null($this->getOrderedAt()) || (!$this->getOrderedAt() instanceof \DateTime)) {
                    $this->setOrderedAt(new \DateTime());
                }
                $this->setDeliveredAt(null);
                break;

            case self::STATUS_CANCELED:
                if (is_null($this->getCanceledAt()) || (!$this->getCanceledAt() instanceof \DateTime)) {
                    $this->setCanceledAt(new \DateTime());
                }
                break;

            case self::STATUS_PLANNED:
            default:
                $this->setOrderedAt(null);
                $this->setDeliveredAt(null);
                break;
        }
    }
}