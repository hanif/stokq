<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="price_histories")
 */
class PriceHistory implements IdProviderInterface
{
    const ALIAS = 'ph';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var MenuPrice
     * @ORM\ManyToOne(targetEntity="MenuPrice", inversedBy="histories")
     * @ORM\JoinColumn(name="menu_price_id", nullable=false, onDelete="CASCADE")
     */
    private $menu_price;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $price = '0';

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     *
     */
    function __construct()
    {
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
     * @return MenuPrice
     */
    public function getMenuPrice()
    {
        return $this->menu_price;
    }

    /**
     * @param MenuPrice $menu_price
     */
    public function setMenuPrice($menu_price)
    {
        $this->menu_price = $menu_price;
    }

    /**
     * @return string
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param string $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

}