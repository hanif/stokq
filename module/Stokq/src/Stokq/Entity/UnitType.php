<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="unit_types")
 */
class UnitType implements IdProviderInterface
{
    const ALIAS = 'ut';

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
     * @ORM\Column(type="string")
     */
    private $description = '';

    /**
     * @var ArrayCollection|StockUnit[]
     * @ORM\OneToMany(targetEntity="StockUnit", mappedBy="type")
     */
    private $stock_units;

    /**
     *
     */
    function __construct()
    {
        $this->stock_units = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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

    /**
     * @return ArrayCollection|StockUnit[]
     */
    public function getStockUnits()
    {
        return $this->stock_units;
    }

    /**
     * @param ArrayCollection|StockUnit[] $stock_units
     */
    public function setStockUnits($stock_units)
    {
        $this->stock_units = $stock_units;
    }

}