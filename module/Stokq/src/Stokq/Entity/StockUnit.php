<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="stock_units")
 */
class StockUnit implements IdProviderInterface
{
    const ALIAS = 'stu';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var UnitType
     * @ORM\ManyToOne(targetEntity="UnitType", inversedBy="stock_units", fetch="EAGER")
     * @ORM\JoinColumn(name="type_id", nullable=false, onDelete="RESTRICT")
     */
    private $type;

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
     * @var double
     * @ORM\Column(type="float")
     */
    private $ratio = 1;

    /**
     *
     */
    function __construct()
    {
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
     * @return float
     */
    public function getRatio()
    {
        return $this->ratio;
    }

    /**
     * @param float $ratio
     */
    public function setRatio($ratio)
    {
        $this->ratio = $ratio;
    }

    /**
     * @return UnitType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param UnitType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}