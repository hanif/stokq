<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="classifications")
 */
class Classification implements IdProviderInterface
{
    const ALIAS = 'f';

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
     * @var ArrayCollection|StockClassification[]
     * @ORM\OneToMany(targetEntity="StockClassification", mappedBy="classification")
     */
    private $stock_classifications;

    /**
     *
     */
    function __construct()
    {
        $this->stock_classifications = new ArrayCollection();
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
     * @return ArrayCollection|StockClassification[]
     */
    public function getStockClassifications()
    {
        return $this->stock_classifications;
    }

    /**
     * @param ArrayCollection|StockClassification[] $stock_classifications
     */
    public function setStockClassifications($stock_classifications)
    {
        $this->stock_classifications = $stock_classifications;
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