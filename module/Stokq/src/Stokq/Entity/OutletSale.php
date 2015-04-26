<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="outlet_sales",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="outlet_sale_unique_outlet_id_and_sale_id",
 *          columns={"outlet_id", "sale_id"}
 *      )}
 * )
 */
class OutletSale implements IdProviderInterface
{
    const ALIAS = 'os';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Outlet
     * @ORM\ManyToOne(targetEntity="Outlet", inversedBy="sales")
     * @ORM\JoinColumn(name="outlet_id", nullable=false, onDelete="CASCADE")
     */
    private $outlet;

    /**
     * @var Sale
     * @ORM\OneToOne(targetEntity="Sale", inversedBy="outlet_sale")
     * @ORM\JoinColumn(name="sale_id", nullable=false, onDelete="CASCADE")
     */
    private $sale;

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
     * @return Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * @param Sale $sale
     */
    public function setSale($sale)
    {
        $this->sale = $sale;
    }

}