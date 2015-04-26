<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="outlets")
 */
class Outlet implements IdProviderInterface
{
    const ALIAS = 'o';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Warehouse
     * @ORM\ManyToOne(targetEntity="Warehouse", inversedBy="outlets", fetch="EAGER")
     * @ORM\JoinColumn(name="warehouse_id", nullable=false, onDelete="CASCADE")
     */
    private $warehouse;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
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
     * @var ArrayCollection|MenuPrice[]
     * @ORM\OneToMany(targetEntity="MenuPrice", mappedBy="outlet")
     */
    private $prices;

    /**
     * @var ArrayCollection|OutletManager[]
     * @ORM\OneToMany(targetEntity="OutletManager", mappedBy="outlet")
     */
    private $managers;

    /**
     * @var ArrayCollection|OutletSale[]
     * @ORM\OneToMany(targetEntity="OutletSale", mappedBy="outlet")
     */
    private $sales;

    /**
     *
     */
    function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->managers = new ArrayCollection();
        $this->sales = new ArrayCollection();
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
     * @return ArrayCollection|OutletManager[]
     */
    public function getManagers()
    {
        return $this->managers;
    }

    /**
     * @param ArrayCollection|OutletManager[] $managers
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
     * @return ArrayCollection|MenuPrice[]
     */
    public function getPrices()
    {
        return $this->prices;
    }

    /**
     * @param ArrayCollection|MenuPrice[] $prices
     */
    public function setPrices($prices)
    {
        $this->prices = $prices;
    }

    /**
     * @return ArrayCollection|OutletSale[]
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param ArrayCollection|OutletSale[] $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }

    /**
     * @return Warehouse
     */
    public function getWarehouse()
    {
        return $this->warehouse;
    }

    /**
     * @param Warehouse $warehouse
     */
    public function setWarehouse($warehouse)
    {
        $this->warehouse = $warehouse;
    }

}