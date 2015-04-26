<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="stock_categories",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="stock_category_unique_stock_item_id_and_category_id",
 *          columns={"stock_item_id", "category_id"}
 *      )}
 * )
 */
class StockCategory implements IdProviderInterface
{
    const ALIAS = 'stc';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var StockItem
     * @ORM\ManyToOne(targetEntity="StockItem", inversedBy="stock_categories", fetch="EAGER")
     * @ORM\JoinColumn(name="stock_item_id", nullable=false, onDelete="CASCADE")
     */
    private $stock_item;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="stock_categories", fetch="EAGER")
     * @ORM\JoinColumn(name="category_id", nullable=false, onDelete="CASCADE")
     */
    private $category;

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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

}