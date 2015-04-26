<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(
 *      name="menu_types",
 *      uniqueConstraints={@ORM\UniqueConstraint(
 *          name="menu_type_unique_menu_id_and_type_id",
 *          columns={"menu_id", "type_id"}
 *      )}
 * )
 */
class MenuType implements IdProviderInterface
{
    const ALIAS = 'mt';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="menu_types")
     * @ORM\JoinColumn(name="menu_id", nullable=false, onDelete="CASCADE")
     */
    private $menu;

    /**
     * @var Type
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="menu_types")
     * @ORM\JoinColumn(name="type_id", nullable=false, onDelete="CASCADE")
     */
    private $type;

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
     * @return Type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

}