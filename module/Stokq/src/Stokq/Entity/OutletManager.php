<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="outlet_managers",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="outlet_manager_unique_user_id_and_outlet_id",
 *          columns={"user_id", "outlet_id"}
 *      )}
 * )
 */
class OutletManager implements IdProviderInterface
{
    const ALIAS = 'om';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="managed_outlets")
     * @ORM\JoinColumn(name="user_id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var Outlet
     * @ORM\ManyToOne(targetEntity="Outlet", inversedBy="managers")
     * @ORM\JoinColumn(name="outlet_id", nullable=false, onDelete="CASCADE")
     */
    private $outlet;

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

}