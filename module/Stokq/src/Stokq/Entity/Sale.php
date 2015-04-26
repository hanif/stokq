<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sales")
 */
class Sale implements IdProviderInterface
{
    const ALIAS = 's';

    const TYPE_ORDER = 'order';
    const TYPE_RECAP = 'recap';
    const TYPE_OTHER = 'other';

    const STATUS_PAID   = 'paid';
    const STATUS_UNPAID = 'unpaid';

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
     * @var string
     * @ORM\Column(type="string")
     */
    private $title = '';

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
    private $type = self::TYPE_ORDER;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $status = self::STATUS_PAID;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    private $ordered_at;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paid_at;

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
     * @var OutletSale
     * @ORM\OneToOne(targetEntity="OutletSale", mappedBy="sale", cascade={"persist", "remove"})
     */
    private $outlet_sale;

    /**
     * @var ArrayCollection|SaleItem[]
     * @ORM\OneToMany(targetEntity="SaleItem", mappedBy="sale", cascade={"persist", "remove"})
     */
    private $items;

    /**
     *
     */
    function __construct()
    {
        $this->items = new ArrayCollection();
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
     * @return ArrayCollection|SaleItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection|SaleItem[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
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
     * @return OutletSale
     */
    public function getOutletSale()
    {
        return $this->outlet_sale;
    }

    /**
     * @param OutletSale $outlet_sale
     */
    public function setOutletSale($outlet_sale)
    {
        $this->outlet_sale = $outlet_sale;
    }

    /**
     * @return \DateTime
     */
    public function getPaidAt()
    {
        return $this->paid_at;
    }

    /**
     * @param \DateTime $paid_at
     */
    public function setPaidAt($paid_at)
    {
        $this->paid_at = $paid_at;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @param Outlet $outlet
     */
    public function setOutlet(Outlet $outlet)
    {
        $outletSale = new OutletSale();
        $outletSale->setOutlet($outlet);
        $outletSale->setSale($this);
        $this->setOutletSale($outletSale);
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
                'menu_price' => $item->getMenuSale() ? $item->getMenuSale()->getMenuPrice()->getId() : null
            ];
        }
        return $items;
    }

}