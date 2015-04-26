<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="stock_classifications",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="stock_classification_unique_stock_item_id_and_classification_id",
 *          columns={"stock_item_id", "classification_id"}
 *      )}
 * )
 */
class StockClassification implements IdProviderInterface
{
    const ALIAS = 'stf';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var StockItem
     * @ORM\ManyToOne(targetEntity="StockItem", inversedBy="stock_classifications")
     * @ORM\JoinColumn(name="stock_item_id", nullable=false, onDelete="CASCADE")
     */
    private $stock_item;

    /**
     * @var Classification
     * @ORM\ManyToOne(targetEntity="Classification", inversedBy="stock_classifications")
     * @ORM\JoinColumn(name="classification_id", nullable=false, onDelete="CASCADE")
     */
    private $classification;

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @return Classification
     */
    public function getClassification()
    {
        return $this->classification;
    }

    /**
     * @param Classification $classification
     */
    public function setClassification($classification)
    {
        $this->classification = $classification;
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