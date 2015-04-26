<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\Collection;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="menus")
 */
class Menu implements IdProviderInterface
{
    const ALIAS = 'm';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var Menu
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="variants", fetch="EAGER")
     * @ORM\JoinColumn(name="parent_id", nullable=true, onDelete="SET NULL")
     */
    private $parent;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $description = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $note = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $serving_unit = '';

    /**
     * @var string
     * @ORM\Column(type="decimal", precision=19, scale=2)
     */
    private $default_price = '0';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $status = self::STATUS_ACTIVE;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var ArrayCollection|Ingredient[]
     * @ORM\OneToMany(targetEntity="Ingredient", mappedBy="menu")
     */
    private $ingredients;

    /**
     * @var ArrayCollection|MenuPrice[]
     * @ORM\OneToMany(targetEntity="MenuPrice", mappedBy="menu")
     */
    private $prices;

    /**
     * @var ArrayCollection|MenuType[]
     * @ORM\OneToMany(targetEntity="MenuType", mappedBy="menu", cascade={"persist", "remove"})
     */
    private $menu_types;

    /**
     * @var ArrayCollection|Menu[]
     * @ORM\OneToMany(targetEntity="Menu", mappedBy="parent")
     */
    private $variants;

    /**
     *
     */
    function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->menu_types = new ArrayCollection();
        $this->variants = new ArrayCollection();
    }

    /**
     * @return Menu
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Menu $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getDefaultPrice()
    {
        return $this->default_price;
    }

    /**
     * @param string $default_price
     */
    public function setDefaultPrice($default_price)
    {
        $this->default_price = $default_price;
    }

    /**
     * @return ArrayCollection|Menu[]
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * @param ArrayCollection|Menu[] $variants
     */
    public function setVariants($variants)
    {
        $this->variants = $variants;
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
     * @return ArrayCollection|Ingredient[]
     */
    public function getIngredients()
    {
        return $this->ingredients;
    }

    /**
     * @param ArrayCollection|Ingredient[] $ingredients
     */
    public function setIngredients($ingredients)
    {
        $this->ingredients = $ingredients;
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
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note)
    {
        $this->note = $note;
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
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getServingUnit()
    {
        return $this->serving_unit;
    }

    /**
     * @param string $serving_unit
     */
    public function setServingUnit($serving_unit)
    {
        $this->serving_unit = $serving_unit;
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
     * @param EntityManager $em
     */
    public function removeAttachedTypes(EntityManager $em)
    {
        $query = $em->createQuery("delete" . " from ent:MenuType mt where mt.menu = :id");
        $query->setParameter('id', $this->getId());
        $query->execute();
    }

    /**
     * Helper method to enable bind from input filter
     *
     * @param array|Type[] $types
     */
    public function setTypes($types)
    {
        if (is_array($types)) {
            Collection::apply($types, function(Type $type) {
                $rel = new MenuType();
                $rel->setType($type);
                $rel->setMenu($this);
                $this->getMenuTypes()->add($rel);
            });
        }
    }
}