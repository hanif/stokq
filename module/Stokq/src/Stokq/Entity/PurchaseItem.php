<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="purchase_items",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="purchase_item_unique_purchase_id_and_item_name",
 *          columns={"purchase_id", "item_name"}
 *      )}
 * )
 */
class PurchaseItem implements IdProviderInterface
{
    const ALIAS = 'pi';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Purchase
     * @ORM\ManyToOne(targetEntity="Purchase", inversedBy="items")
     * @ORM\JoinColumn(name="purchase_id", nullable=false, onDelete="CASCADE")
     */
    private $purchase;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $item_name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $note = '';

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $unit_price = '0';

    /**
     * @var double
     * @ORM\Column(type="float")
     */
    private $quantity = 0;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $unit = '';

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $subtotal = '0';

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $total = '0';

    /**
     * @var StockPurchase
     * @ORM\OneToOne(targetEntity="StockPurchase", mappedBy="purchase_item")
     */
    private $stock_purchase;

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
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
     * @return string
     */
    public function getItemName()
    {
        return $this->item_name;
    }

    /**
     * @param string $item_name
     */
    public function setItemName($item_name)
    {
        $this->item_name = $item_name;
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
     * @return Purchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param Purchase $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param float $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return StockPurchase
     */
    public function getStockPurchase()
    {
        return $this->stock_purchase;
    }

    /**
     * @param StockPurchase $stock_purchase
     */
    public function setStockPurchase($stock_purchase)
    {
        $this->stock_purchase = $stock_purchase;
    }

    /**
     * @return string
     */
    public function getSubtotal()
    {
        return $this->subtotal;
    }

    /**
     * @param string $subtotal
     */
    public function setSubtotal($subtotal)
    {
        $this->subtotal = $subtotal;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getUnitPrice()
    {
        return $this->unit_price;
    }

    /**
     * @param string $unit_price
     */
    public function setUnitPrice($unit_price)
    {
        $this->unit_price = $unit_price;
    }

}