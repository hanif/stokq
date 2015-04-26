<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="level_changes")
 */
class LevelChange implements IdProviderInterface
{
    const ALIAS = 'lc';

    const TYPE_USAGE        = 'usage';
    const TYPE_PURCHASE     = 'purchase';
    const TYPE_CORRECTION   = 'correction';
    const TYPE_MUTATION     = 'mutation';
    const TYPE_WASTED       = 'wasted';
    const TYPE_STOCK_IN     = 'stock in';
    const TYPE_STOCK_OUT    = 'stock out';
    const TYPE_OTHER        = 'other';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="corrector_id", nullable=true, onDelete="SET NULL")
     */
    private $corrector;

    /**
     * @var Stock
     * @ORM\ManyToOne(targetEntity="Stock", inversedBy="level_changes")
     * @ORM\JoinColumn(name="stock_id", nullable=false, onDelete="CASCADE")
     */
    private $stock;

    /**
     * @var double
     * @ORM\Column(type="float")
     */
    private $current_level;

    /**
     * @var double
     * @ORM\Column(type="float")
     */
    private $delta = 0;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $auto = false;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $type = self::TYPE_CORRECTION;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $note = '';

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @return boolean
     */
    public function isAuto()
    {
        return $this->auto;
    }

    /**
     * @param boolean $auto
     */
    public function setAuto($auto)
    {
        $this->auto = $auto;
    }

    /**
     * @return User
     */
    public function getCorrector()
    {
        return $this->corrector;
    }

    /**
     * @param User $corrector
     */
    public function setCorrector($corrector)
    {
        $this->corrector = $corrector;
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
     * @return float
     */
    public function getCurrentLevel()
    {
        return $this->current_level;
    }

    /**
     * @param float $current_level
     */
    public function setCurrentLevel($current_level)
    {
        $this->current_level = $current_level;
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
     * @return Stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @param Stock $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return float
     */
    public function getDelta()
    {
        return $this->delta;
    }

    /**
     * @param float $delta
     */
    public function setDelta($delta)
    {
        $this->delta = $delta;
    }

    /**
     * @return string
     */
    public function getNewLevel()
    {
        return $this->getCurrentLevel() + $this->getDelta();
    }

    /**
     * @return float
     */
    public function getCurrentLevelInStorageUnit()
    {
        return $this->getCurrentLevel() / $this->getStock()->getStockItem()->getStorageUnit()->getRatio();
    }

    /**
     * @return float
     */
    public function getNewLevelInStorageUnit()
    {
        return $this->getNewLevel() / $this->getStock()->getStockItem()->getStorageUnit()->getRatio();
    }

    /**
     * @return float
     */
    public function getDeltaInStorageUnit()
    {
        return $this->getDelta() / $this->getStock()->getStockItem()->getStorageUnit()->getRatio();
    }
}