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
 * @ORM\Table(name="stock_items")
 */
class StockItem implements IdProviderInterface
{
    const ALIAS = 'sti';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var StorageType
     * @ORM\ManyToOne(targetEntity="StorageType", inversedBy="items", fetch="EAGER")
     * @ORM\JoinColumn(name="type_id", nullable=true, onDelete="SET NULL")
     */
    private $type;

    /**
     * @var StockUnit
     * @ORM\ManyToOne(targetEntity="StockUnit", fetch="EAGER")
     * @ORM\JoinColumn(name="storage_unit_id", nullable=false, onDelete="RESTRICT")
     */
    private $storage_unit;

    /**
     * @var StockUnit
     * @ORM\ManyToOne(targetEntity="StockUnit", fetch="EAGER")
     * @ORM\JoinColumn(name="usage_unit_id", nullable=false, onDelete="RESTRICT")
     */
    private $usage_unit;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=true)
     */
    private $code;

    /**
     * @var string
     * @ORM\Column(type="string")
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
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var ArrayCollection|Ingredient[]
     * @ORM\OneToMany(targetEntity="Ingredient", mappedBy="stock_item")
     */
    private $ingredients;

    /**
     * @var ArrayCollection|Stock[]
     * @ORM\OneToMany(targetEntity="Stock", mappedBy="stock_item")
     */
    private $stocks;

    /**
     * @var ArrayCollection|StockCategory[]
     * @ORM\OneToMany(targetEntity="StockCategory", mappedBy="stock_item", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $stock_categories;

    /**
     * @var ArrayCollection|StockClassification[]
     * @ORM\OneToMany(targetEntity="StockClassification", mappedBy="stock_item", cascade={"persist", "remove"})
     */
    private $stock_classifications;

    /**
     *
     */
    function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->stocks = new ArrayCollection();
        $this->stock_categories = new ArrayCollection();
        $this->stock_classifications = new ArrayCollection();
    }

    /**
     * @return StorageType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param StorageType $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * @return ArrayCollection|Stock[]
     */
    public function getStocks()
    {
        return $this->stocks;
    }

    /**
     * @param ArrayCollection|Stock[] $stocks
     */
    public function setStocks($stocks)
    {
        $this->stocks = $stocks;
    }

    /**
     * @return StockUnit
     */
    public function getStorageUnit()
    {
        return $this->storage_unit;
    }

    /**
     * @param StockUnit $storage_unit
     */
    public function setStorageUnit($storage_unit)
    {
        $this->storage_unit = $storage_unit;
    }

    /**
     * @return StockUnit
     */
    public function getUsageUnit()
    {
        return $this->usage_unit;
    }

    /**
     * @param StockUnit $usage_unit
     */
    public function setUsageUnit($usage_unit)
    {
        $this->usage_unit = $usage_unit;
    }

    /**
     * @return ArrayCollection|StockCategory[]
     */
    public function getStockCategories()
    {
        return $this->stock_categories;
    }

    /**
     * @param ArrayCollection|StockCategory[] $stock_categories
     */
    public function setStockCategories($stock_categories)
    {
        $this->stock_categories = $stock_categories;
    }

    /**
     * @return ArrayCollection|StockClassification[]
     */
    public function getStockClassifications()
    {
        return $this->stock_classifications;
    }

    /**
     * @param ArrayCollection|StockClassification[] $stock_classifications
     */
    public function setStockClassifications($stock_classifications)
    {
        $this->stock_classifications = $stock_classifications;
    }

    /**
     * @param EntityManager $em
     */
    public function removeAttachedCategories(EntityManager $em)
    {
        $query = $em->createQuery("delete" . " from ent:StockCategory sc where sc.stock_item = :id");
        $query->setParameter('id', $this->getId());
        $query->execute();
    }

    /**
     * Helper method to enable bind from input filter
     *
     * @param array|Category[] $categories
     */
    public function setCategories($categories)
    {
        if (is_array($categories)) {
            Collection::apply($categories, function(Category $category) {
                $rel = new StockCategory();
                $rel->setCategory($category);
                $rel->setStockItem($this);
                $this->getStockCategories()->add($rel);
            });
        }
    }
}