<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="stocks",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="stock_unique_warehouse_id_and_stock_item_id",
 *          columns={"warehouse_id", "stock_item_id"}
 *      )}
 * )
 */
class Stock implements IdProviderInterface
{
    const ALIAS = 'st';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Warehouse
     * @ORM\ManyToOne(targetEntity="Warehouse", inversedBy="stocks")
     * @ORM\JoinColumn(name="warehouse_id", nullable=false, onDelete="CASCADE")
     */
    private $warehouse;

    /**
     * @var StockItem
     * @ORM\ManyToOne(targetEntity="StockItem", inversedBy="stocks")
     * @ORM\JoinColumn(name="stock_item_id", nullable=false, onDelete="CASCADE")
     */
    private $stock_item;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2, nullable=true)
     */
    private $current_unit_price;

    /**
     * @var double
     * @ORM\Column(type="float")
     */
    private $current_level = 0;

    /**
     * @var double
     * @ORM\Column(type="float", nullable=true)
     */
    private $reorder_level = 0;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_change;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_purchase;

    /**
     * @var ArrayCollection|LevelChange[]
     * @ORM\OneToMany(targetEntity="LevelChange", mappedBy="stock")
     */
    private $level_changes;

    /**
     * @var ArrayCollection|StockPurchase[]
     * @ORM\OneToMany(targetEntity="StockPurchase", mappedBy="stock")
     */
    private $purchases;

    /**
     *
     */
    function __construct()
    {
        $this->level_changes = new ArrayCollection();
        $this->purchases = new ArrayCollection();
    }

    /**
     * @return float
     */
    public function getCurrentLevel()
    {
        return $this->current_level;
    }

    /**
     * @param float $current_level
     */
    public function setCurrentLevel($current_level)
    {
        $this->current_level = $current_level;
    }

    /**
     * @return string
     */
    public function getCurrentUnitPrice()
    {
        return $this->current_unit_price;
    }

    /**
     * @param string $current_unit_price
     */
    public function setCurrentUnitPrice($current_unit_price)
    {
        $this->current_unit_price = $current_unit_price;
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
     * @return \DateTime
     */
    public function getLastChange()
    {
        return $this->last_change;
    }

    /**
     * @param \DateTime $last_change
     */
    public function setLastChange($last_change)
    {
        $this->last_change = $last_change;
    }

    /**
     * @return \DateTime
     */
    public function getLastPurchase()
    {
        return $this->last_purchase;
    }

    /**
     * @param \DateTime $last_purchase
     */
    public function setLastPurchase($last_purchase)
    {
        $this->last_purchase = $last_purchase;
    }

    /**
     * @return ArrayCollection|LevelChange[]
     */
    public function getLevelChanges()
    {
        return $this->level_changes;
    }

    /**
     * @param ArrayCollection|LevelChange[] $level_changes
     */
    public function setLevelChanges($level_changes)
    {
        $this->level_changes = $level_changes;
    }

    /**
     * @return ArrayCollection|StockPurchase[]
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @param ArrayCollection|StockPurchase[] $purchases
     */
    public function setPurchases($purchases)
    {
        $this->purchases = $purchases;
    }

    /**
     * @return float
     */
    public function getReorderLevel()
    {
        return $this->reorder_level;
    }

    /**
     * @param float $reorder_level
     */
    public function setReorderLevel($reorder_level)
    {
        $this->reorder_level = $reorder_level;
    }

    /**
     * @return StockItem
     */
    public function getStockItem()
    {
        return $this->stock_item;
    }

    /**
     * @param StockItem $stock_item
     */
    public function setStockItem($stock_item)
    {
        $this->stock_item = $stock_item;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

}