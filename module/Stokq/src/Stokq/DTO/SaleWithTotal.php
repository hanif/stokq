<?php

namespace Stokq\DTO;

/**
 * Class SaleWithTotal
 * @package Stokq\DTO
 */
class SaleWithTotal
{
    /**
     * @var int
     */
    private $creatorId;

    /**
     * @var string
     */
    private $creatorName;

    /**
     * @var int
     */
    private $outletId;

    /**
     * @var string
     */
    private $outletName;

    /**
     * @var int
     */
    private $saleId;

    /**
     * @var string
     */
    private $saleTitle;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $type;

    /**
     * @var \DateTime
     */
    private $orderedAt;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var int
     */
    private $numItems;

    /**
     * @var double
     */
    private $numQuantity;

    /**
     * @var string
     */
    private $total;

    /**
     * @param int $creatorId
     * @param string $creatorName
     * @param int $outletId
     * @param string $outletName
     * @param int $saleId
     * @param string $saleTitle
     * @param string $currency
     * @param string $type
     * @param \DateTime $orderedAt
     * @param \DateTime $createdAt
     * @param int $numItems
     * @param double $numQuantity
     * @param string $total
     */
    function __construct($creatorId, $creatorName, $outletId, $outletName, $saleId, $saleTitle, $currency, $type, $orderedAt, $createdAt, $numItems, $numQuantity, $total)
    {
        $this->creatorId = $creatorId;
        $this->creatorName = $creatorName;
        $this->outletId = $outletId;
        $this->outletName = $outletName;
        $this->saleId = $saleId;
        $this->saleTitle = $saleTitle;
        $this->currency = $currency;
        $this->type = $type;
        $this->orderedAt = $orderedAt;
        $this->createdAt = $createdAt;
        $this->numItems = $numItems;
        $this->numQuantity = $numQuantity;
        $this->total = $total;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getNumItems()
    {
        return $this->numItems;
    }

    /**
     * @param int $numItems
     */
    public function setNumItems($numItems)
    {
        $this->numItems = $numItems;
    }

    /**
     * @return float
     */
    public function getNumQuantity()
    {
        return $this->numQuantity;
    }

    /**
     * @param float $numQuantity
     */
    public function setNumQuantity($numQuantity)
    {
        $this->numQuantity = $numQuantity;
    }

    /**
     * @return \DateTime
     */
    public function getOrderedAt()
    {
        return $this->orderedAt;
    }

    /**
     * @return string
     */
    public function getFormattedOrderedAt()
    {
        if (!$this->orderedAt instanceof \DateTime) {
            return '';
        }

        return $this->orderedAt->format('M d, Y');
    }

    /**
     * @param \DateTime $orderedAt
     */
    public function setOrderedAt($orderedAt)
    {
        $this->orderedAt = $orderedAt;
    }

    /**
     * @return int
     */
    public function getOutletId()
    {
        return $this->outletId;
    }

    /**
     * @param int $outletId
     */
    public function setOutletId($outletId)
    {
        $this->outletId = $outletId;
    }

    /**
     * @return string
     */
    public function getOutletName()
    {
        return $this->outletName;
    }

    /**
     * @param string $outletName
     */
    public function setOutletName($outletName)
    {
        $this->outletName = $outletName;
    }

    /**
     * @return int
     */
    public function getSaleId()
    {
        return $this->saleId;
    }

    /**
     * @param int $saleId
     */
    public function setSaleId($saleId)
    {
        $this->saleId = $saleId;
    }

    /**
     * @return string
     */
    public function getSaleTitle()
    {
        return $this->saleTitle;
    }

    /**
     * @param string $saleTitle
     */
    public function setSaleTitle($saleTitle)
    {
        $this->saleTitle = $saleTitle;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
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
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getFormattedCreatedAt()
    {
        return $this->createdAt->format('M d, Y');
    }

    /**
     * @return string
     */
    public function getCreatedAtAsYmd()
    {
        return $this->createdAt->format('ymd');
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return int
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * @param int $creatorId
     */
    public function setCreatorId($creatorId)
    {
        $this->creatorId = $creatorId;
    }

    /**
     * @return string
     */
    public function getCreatorName()
    {
        return $this->creatorName;
    }

    /**
     * @param string $creatorName
     */
    public function setCreatorName($creatorName)
    {
        $this->creatorName = $creatorName;
    }

}