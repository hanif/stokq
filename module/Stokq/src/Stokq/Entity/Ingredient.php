<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity(repositoryClass="\Gedmo\Sortable\Entity\Repository\SortableRepository")
 * @ORM\Table(
 *      name="ingredients",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="ingredient_unique_menu_id_and_stock_item_id",
 *          columns={"menu_id", "stock_item_id"}
 *      )}
 * )
 */
class Ingredient implements IdProviderInterface
{
    const ALIAS = 'i';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var IngredientType
     * @ORM\ManyToOne(targetEntity="IngredientType", inversedBy="ingredients")
     * @ORM\JoinColumn(name="type_id", nullable=false, onDelete="CASCADE")
     */
    private $type;

    /**
     * @var Menu
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="ingredients")
     * @ORM\JoinColumn(name="menu_id", nullable=false, onDelete="CASCADE")
     */
    private $menu;

    /**
     * @var StockItem
     * @ORM\ManyToOne(targetEntity="StockItem", inversedBy="ingredients")
     * @ORM\JoinColumn(name="stock_item_id", nullable=false, onDelete="CASCADE")
     */
    private $stock_item;

    /**
     * @var int
     * @Gedmo\SortablePosition
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @var double
     * @ORM\Column(type="float")
     */
    private $quantity = 0;

    /**
     * @var number
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $qty_price = '0';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $note = '';

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @return IngredientType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param IngredientType $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
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
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
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
     * @return number
     */
    public function getQtyPrice()
    {
        return $this->qty_price;
    }

    /**
     * @param number $qty_price
     */
    public function setQtyPrice($qty_price)
    {
        $this->qty_price = $qty_price;
    }

}