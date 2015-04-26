<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="warehouse_purchases",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="warehouse_purchase_unique_warehouse_id_and_purchase_id",
 *          columns={"warehouse_id", "purchase_id"}
 *      )}
 * )
 */
class WarehousePurchase implements IdProviderInterface
{
    const ALIAS = 'wp';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Warehouse
     * @ORM\ManyToOne(targetEntity="Warehouse", inversedBy="purchases")
     * @ORM\JoinColumn(name="warehouse_id", nullable=false, onDelete="CASCADE")
     */
    private $warehouse;

    /**
     * @var Purchase
     * @ORM\OneToOne(targetEntity="Purchase", inversedBy="warehouse_purchase")
     * @ORM\JoinColumn(name="purchase_id", nullable=false, onDelete="CASCADE")
     */
    private $purchase;

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
     * @return Purchase
     */
    public function getPurchase()
    {
        return $this->purchase;
    }

    /**
     * @param Purchase $purchase
     */
    public function setPurchase($purchase)
    {
        $this->purchase = $purchase;
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