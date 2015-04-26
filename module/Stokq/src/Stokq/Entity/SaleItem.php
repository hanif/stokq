<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="sale_items",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="sale_item_unique_sale_id_and_item_name",
 *          columns={"sale_id", "item_name"}
 *      )}
 * )
 */
class SaleItem implements IdProviderInterface
{
    const ALIAS = 'si';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Sale
     * @ORM\ManyToOne(targetEntity="Sale", inversedBy="items")
     * @ORM\JoinColumn(name="sale_id", nullable=false, onDelete="CASCADE")
     */
    private $sale;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $item_name;

    /**
     * @var double
     * @ORM\Column(type="float")
     */
    private $quantity = 0;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $unit_price = '0';

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
     * @var string
     * @ORM\Column(type="text")
     */
    private $note = '';

    /**
     * @var MenuSale
     * @ORM\OneToOne(targetEntity="MenuSale", mappedBy="sale_item", cascade={"persist", "remove"})
     */
    private $menu_sale;

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
     * @return MenuSale
     */
    public function getMenuSale()
    {
        return $this->menu_sale;
    }

    /**
     * @param MenuSale $menu_sale
     */
    public function setMenuSale($menu_sale)
    {
        $this->menu_sale = $menu_sale;
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
     * @return double
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param double $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * @param Sale $sale
     */
    public function setSale($sale)
    {
        $this->sale = $sale;
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