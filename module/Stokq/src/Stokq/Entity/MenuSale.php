<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="menu_sales",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="menu_sale_unique_menu_price_id_and_sale_item_id",
 *          columns={"menu_price_id", "sale_item_id"}
 *      )}
 * )
 */
class MenuSale implements IdProviderInterface
{
    const ALIAS = 'ms';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var MenuPrice
     * @ORM\ManyToOne(targetEntity="MenuPrice", inversedBy="sales")
     * @ORM\JoinColumn(name="menu_price_id", nullable=false, onDelete="CASCADE")
     */
    private $menu_price;

    /**
     * @var SaleItem
     * @ORM\OneToOne(targetEntity="SaleItem", inversedBy="menu_sale")
     * @ORM\JoinColumn(name="sale_item_id", nullable=false, onDelete="CASCADE")
     */
    private $sale_item;

    /**
     *
     */
    function __construct()
    {
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
     * @return SaleItem
     */
    public function getSaleItem()
    {
        return $this->sale_item;
    }

    /**
     * @param SaleItem $sale_item
     */
    public function setSaleItem($sale_item)
    {
        $this->sale_item = $sale_item;
    }

}