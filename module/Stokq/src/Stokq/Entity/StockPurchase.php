<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="stock_purchases",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="stock_purchase_unique_stock_id_and_purchase_item_id",
 *          columns={"stock_id", "purchase_item_id"}
 *      )}
 * )
 */
class StockPurchase implements IdProviderInterface
{
    const ALIAS = 'stp';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Stock
     * @ORM\ManyToOne(targetEntity="Stock", inversedBy="purchases")
     * @ORM\JoinColumn(name="stock_id", nullable=false, onDelete="CASCADE")
     */
    private $stock;

    /**
     * @var PurchaseItem
     * @ORM\OneToOne(targetEntity="PurchaseItem", inversedBy="stock_purchase")
     * @ORM\JoinColumn(name="purchase_item_id", nullable=false, onDelete="CASCADE")
     */
    private $purchase_item;

    /**
     *
     */
    function __construct()
    {
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
     * @return PurchaseItem
     */
    public function getPurchaseItem()
    {
        return $this->purchase_item;
    }

    /**
     * @param PurchaseItem $purchase_item
     */
    public function setPurchaseItem($purchase_item)
    {
        $this->purchase_item = $purchase_item;
    }

    /**
     * @return Stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param Stock $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

}