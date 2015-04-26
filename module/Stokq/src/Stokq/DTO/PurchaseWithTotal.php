<?php

namespace Stokq\DTO;

/**
 * Class PurchaseWithTotal
 * @package Stokq\DTO
 */
class PurchaseWithTotal
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
    private $warehouseId;

    /**
     * @var string
     */
    private $warehouseName;

    /**
     * @var int
     */
    private $purchaseId;

    /**
     * @var string
     */
    private $purchaseTitle;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $orderedAt;

    /**
     * @var \DateTime
     */
    private $deliveredAt;

    /**
     * @var \DateTime
     */
    private $canceledAt;

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
     * @param int $warehouseId
     * @param string $warehouseName
     * @param int $purchaseId
     * @param string $purchaseTitle
     * @param string $currency
     * @param string $status
     * @param \DateTime|null $orderedAt
     * @param \DateTime|null $deliveredAt
     * @param \DateTime|null $canceledAt
     * @param \DateTime $createdAt
     * @param double $numItems
     * @param double $numQuantity
     * @param double $total
     */
    function __construct($creatorId, $creatorName, $warehouseId, $warehouseName, $purchaseId, $purchaseTitle, $currency, $status, $orderedAt, $deliveredAt, $canceledAt, $createdAt, $numItems, $numQuantity, $total)
    {
        $this->creatorId = $creatorId;
        $this->creatorName = $creatorName;
        $this->warehouseId = $warehouseId;
        $this->warehouseName = $warehouseName;
        $this->purchaseId = $purchaseId;
        $this->purchaseTitle = $purchaseTitle;
        $this->currency = $currency;
        $this->status = $status;
        $this->orderedAt = $orderedAt;
        $this->deliveredAt = $deliveredAt;
        $this->canceledAt = $canceledAt;
        $this->createdAt = $createdAt;
        $this->numItems = $numItems;
        $this->numQuantity = $numQuantity;
        $this->total = $total;
    }

    /**
     * @return \DateTime
     */
    public function getCanceledAt()
    {
        return $this->canceledAt;
    }

    /**
     * @return string
     */
    public function getFormattedCanceledAt()
    {
        if (!$this->canceledAt instanceof \DateTime) {
            return '';
        }

        return $this->canceledAt->format('M d, Y');
    }

    /**
     * @param \DateTime $canceledAt
     */
    public function setCanceledAt($canceledAt)
    {
        $this->canceledAt = $canceledAt;
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
    public function getCreatedAtAsYmd()
    {
        return $this->createdAt->format('ymd');
    }

    /**
     * @return string
     */
    public function getFormattedCreatedAt()
    {
        return $this->createdAt->format('M d, Y');
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
     * @return \DateTime
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * @return string
     */
    public function getFormattedDeliveredAt()
    {
        if (!$this->deliveredAt instanceof \DateTime) {
            return '';
        }

        return $this->deliveredAt->format('M d, Y');
    }

    /**
     * @param \DateTime $deliveredAt
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;
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
    public function getPurchaseId()
    {
        return $this->purchaseId;
    }

    /**
     * @param int $purchaseId
     */
    public function setPurchaseId($purchaseId)
    {
        $this->purchaseId = $purchaseId;
    }

    /**
     * @return string
     */
    public function getPurchaseTitle()
    {
        return $this->purchaseTitle;
    }

    /**
     * @param string $purchaseTitle
     */
    public function setPurchaseTitle($purchaseTitle)
    {
        $this->purchaseTitle = $purchaseTitle;
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
     * @return int
     */
    public function getWarehouseId()
    {
        return $this->warehouseId;
    }

    /**
     * @param int $warehouseId
     */
    public function setWarehouseId($warehouseId)
    {
        $this->warehouseId = $warehouseId;
    }

    /**
     * @return string
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     * @param string $warehouseName
     */
    public function setWarehouseName($warehouseName)
    {
        $this->warehouseName = $warehouseName;
    }

}