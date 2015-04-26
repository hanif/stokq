<?php

namespace Stokq\Flow;

use Stokq\Entity\Purchase;
use Stokq\InputFilter\PurchaseInput;
use Stokq\Validator\Workflow;

/**
 * Class AccountForm
 * @package Stokq\Flow
 */
class PurchaseFlow
{
    const DEFAULT_INPUT_DATE_FORMAT = 'M d, Y';

    /**
     * @var string
     */
    protected $inputDateFormat = self::DEFAULT_INPUT_DATE_FORMAT;

    /**
     * @var Purchase
     */
    protected $purchase;

    /**
     * @var string
     */
    protected $orderedAt;

    /**
     * @var string
     */
    protected $canceledAt;

    /**
     * @var string
     */
    protected $deliveredAt;

    /**
     * @param Purchase $purchase
     */
    function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * @return string
     */
    public function getCanceledAt()
    {
        return $this->canceledAt;
    }

    /**
     * @param string $canceledAt
     */
    public function setCanceledAt($canceledAt)
    {
        $this->canceledAt = $canceledAt;
    }

    /**
     * @return string
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * @param string $deliveredAt
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;
    }

    /**
     * @return string
     */
    public function getOrderedAt()
    {
        return $this->orderedAt;
    }

    /**
     * @param string $orderedAt
     */
    public function setOrderedAt($orderedAt)
    {
        $this->orderedAt = $orderedAt;
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
     * @return string
     */
    public function getInputDateFormat()
    {
        return $this->inputDateFormat;
    }

    /**
     * @param string $inputDateFormat
     */
    public function setInputDateFormat($inputDateFormat)
    {
        $this->inputDateFormat = $inputDateFormat;
    }

    /**
     * @param $status
     * @return bool
     */
    public function to($status)
    {
        if ($status != $this->purchase->getStatus()) {
            $workflow = new Workflow([
                'workflow' => PurchaseInput::getStatusOptions(),
                'from' => $this->purchase->getStatus(),
                'allow_jumps' => true,
                'allow_current' => false,
            ]);

            if ($workflow->isValid($status)) {
                switch ($status) {
                    case Purchase::STATUS_DELIVERED:
                        $this->assignOrderDate(false);
                        $this->assignDeliveryDate(false);
                        break;

                    case Purchase::STATUS_IN_PROGRESS:
                        $this->assignOrderDate(false);
                        $this->purchase->setDeliveredAt(null);
                        break;

                    case Purchase::STATUS_CANCELED:
                        $this->assignOrderDate(true);
                        $this->assignCancelDate(false);
                        $this->purchase->setDeliveredAt(null);
                        break;

                    case Purchase::STATUS_PLANNED:
                    default:
                        $this->purchase->setOrderedAt(null);
                        $this->purchase->setDeliveredAt(null);
                        break;
                }

                return true;
            }
        }

        throw new \RuntimeException('Invalid workflow');
    }

    /**
     * @param $optional
     */
    protected function assignOrderDate($optional)
    {
        if ($orderedAt = $this->getOrderedAt()) {
            if (!$orderedAt instanceof \DateTime) {
                $orderedAt = \DateTime::createFromFormat($this->getInputDateFormat(), $orderedAt);
                if (!$orderedAt instanceof \DateTime) {
                    $orderedAt = new \DateTime();
                }
            }
            $this->purchase->setOrderedAt($orderedAt);
        } else {
            if ((!$this->purchase->getOrderedAt() instanceof \DateTime) && !$optional) {
                $this->purchase->setOrderedAt(new \DateTime());
            }
        }
    }

    /**
     * @param $optional
     */
    protected function assignDeliveryDate($optional)
    {
        if ($deliveredAt = $this->getDeliveredAt()) {
            if (!$deliveredAt instanceof \DateTime) {
                $deliveredAt = \DateTime::createFromFormat($this->getInputDateFormat(), $deliveredAt);
                if (!$deliveredAt instanceof \DateTime) {
                    $deliveredAt = new \DateTime();
                }
            }
            $this->purchase->setDeliveredAt($deliveredAt);
        } else {
            if ((!$this->purchase->getDeliveredAt() instanceof \DateTime) && !$optional) {
                $this->purchase->setDeliveredAt(new \DateTime());
            }
        }
    }

    /**
     * @param $optional
     */
    protected function assignCancelDate($optional)
    {
        if ($canceledAt = $this->getCanceledAt()) {
            if (!$canceledAt instanceof \DateTime) {
                $canceledAt = \DateTime::createFromFormat($this->getInputDateFormat(), $canceledAt);
                if (!$canceledAt instanceof \DateTime) {
                    $canceledAt = new \DateTime();
                }
            }
            $this->purchase->setCanceledAt($canceledAt);
        } else {
            if ((!$this->purchase->getCanceledAt() instanceof \DateTime) && !$optional) {
                $this->purchase->setCanceledAt(new \DateTime());
            }
        }
    }
}