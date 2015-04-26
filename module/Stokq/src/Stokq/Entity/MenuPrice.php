<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="menu_prices",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="menu_price_unique_outlet_id_and_menu_id",
 *          columns={"outlet_id", "menu_id"}
 *      )}
 * )
 */
class MenuPrice implements IdProviderInterface
{
    const ALIAS = 'mp';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Outlet
     * @ORM\ManyToOne(targetEntity="Outlet", inversedBy="prices")
     * @ORM\JoinColumn(name="outlet_id", nullable=false, onDelete="CASCADE")
     */
    private $outlet;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="prices")
     * @ORM\JoinColumn(name="menu_id", nullable=false, onDelete="CASCADE")
     */
    private $menu;

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $price = '0';

    /**
     * @var ArrayCollection|MenuSale[]
     * @ORM\OneToMany(targetEntity="MenuSale", mappedBy="menu_price")
     */
    private $sales;

    /**
     * @var ArrayCollection|PriceHistory[]
     * @ORM\OneToMany(targetEntity="PriceHistory", mappedBy="menu_price")
     */
    private $histories;

    /**
     *
     */
    function __construct()
    {
        $this->sales = new ArrayCollection();
        $this->histories = new ArrayCollection();
    }

    /**
     * @return ArrayCollection|PriceHistory[]
     */
    public function getHistories()
    {
        return $this->histories;
    }

    /**
     * @param ArrayCollection|PriceHistory[] $histories
     */
    public function setHistories($histories)
    {
        $this->histories = $histories;
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
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param Menu $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return Outlet
     */
    public function getOutlet()
    {
        return $this->outlet;
    }

    /**
     * @param Outlet $outlet
     */
    public function setOutlet($outlet)
    {
        $this->outlet = $outlet;
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

    /**
     * @return ArrayCollection|MenuSale[]
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * @param ArrayCollection|MenuSale[] $sales
     */
    public function setSales($sales)
    {
        $this->sales = $sales;
    }

}