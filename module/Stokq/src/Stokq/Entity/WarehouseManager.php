<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="warehouse_managers",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="warehouse_manager_unique_user_id_and_warehouse_id",
 *          columns={"user_id", "warehouse_id"}
 *      )}
 * )
 */
class WarehouseManager implements IdProviderInterface
{
    const ALIAS = 'wm';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="managed_warehouses")
     * @ORM\JoinColumn(name="user_id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Warehouse
     * @ORM\ManyToOne(targetEntity="Warehouse", inversedBy="managers")
     * @ORM\JoinColumn(name="warehouse_id", nullable=false, onDelete="CASCADE")
     */
    private $warehouse;

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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
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