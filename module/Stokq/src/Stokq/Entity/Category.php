<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="categories")
 */
class Category implements IdProviderInterface
{
    const ALIAS = 'c';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $color;

    /**
     * @var ArrayCollection|StockCategory[]
     * @ORM\OneToMany(targetEntity="StockCategory", mappedBy="category")
     */
    private $stock_categories;

    /**
     *
     */
    function __construct()
    {
        $this->stock_categories = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
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
     * @return ArrayCollection|StockCategory[]
     */
    public function getStockCategories()
    {
        return $this->stock_categories;
    }

    /**
     * @param ArrayCollection|StockCategory[] $stock_categories
     */
    public function setStockCategories($stock_categories)
    {
        $this->stock_categories = $stock_categories;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

}