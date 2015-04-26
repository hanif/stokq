<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="storage_types")
 */
class StorageType implements IdProviderInterface
{
    const ALIAS = 'stt';

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
     * @var ArrayCollection|StockItem[]
     * @ORM\OneToMany(targetEntity="StockItem", mappedBy="type")
     */
    private $items;

    /**
     *
     */
    function __construct()
    {
        $this->items = new ArrayCollection();
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
     * @return ArrayCollection|StockItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param ArrayCollection|StockItem[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
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