<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="types")
 */
class Type implements IdProviderInterface
{
    const ALIAS = 'ty';

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
     * @var ArrayCollection|MenuType[]
     * @ORM\OneToMany(targetEntity="MenuType", mappedBy="type")
     */
    private $menu_types;

    /**
     *
     */
    function __construct()
    {
        $this->menu_types = new ArrayCollection();
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
     * @return ArrayCollection|MenuType[]
     */
    public function getMenuTypes()
    {
        return $this->menu_types;
    }

    /**
     * @param ArrayCollection|MenuType[] $menu_types
     */
    public function setMenuTypes($menu_types)
    {
        $this->menu_types = $menu_types;
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

}