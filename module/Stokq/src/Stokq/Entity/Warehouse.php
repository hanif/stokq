<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="warehouses")
 */
class Warehouse implements IdProviderInterface
{
    const ALIAS = 'w';

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
     * @var string
     * @ORM\Column(type="string")
     */
    private $address = '';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $latitude;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $longitude;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var ArrayCollection|Outlet[]
     * @ORM\OneToMany(targetEntity="Outlet", mappedBy="warehouse")
     */
    private $outlets;

    /**
     * @var ArrayCollection|Stock[]
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="warehouse")
     */
    private $stocks;

    /**
     * @var ArrayCollection|WarehouseManager[]
     * @ORM\OneToMany(targetEntity="WarehouseManager", mappedBy="warehouse")
     */
    private $managers;

    /**
     * @var ArrayCollection|WarehousePurchase[]
     * @ORM\OneToMany(targetEntity="WarehousePurchase", mappedBy="warehouse")
     */
    private $purchases;

    /**
     *
     */
    function __construct()
    {
        $this->outlets = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->purchases = new ArrayCollection();
        $this->managers = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
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
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return string
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @return ArrayCollection|WarehouseManager[]
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * @param ArrayCollection|WarehouseManager[] $managers
     */
    public function setManagers($managers)
    {
        $this->managers = $managers;
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
     * @return ArrayCollection|Outlet[]
     */
    public function getOutlets()
    {
        return $this->outlets;
    }

    /**
     * @param ArrayCollection|Outlet[] $outlets
     */
    public function setOutlets($outlets)
    {
        $this->outlets = $outlets;
    }

    /**
     * @return ArrayCollection|WarehousePurchase[]
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * @param ArrayCollection|WarehousePurchase[] $purchases
     */
    public function setPurchases($purchases)
    {
        $this->purchases = $purchases;
    }

    /**
     * @return ArrayCollection|Stock[]
     */
    public function getStocks()
    {
        return $this->stocks;
    }

    /**
     * @param ArrayCollection|Stock[] $stocks
     */
    public function setStocks($stocks)
    {
        $this->stocks = $stocks;
    }

}