<?php

namespace Stokq\DTO;

/**
 * Class SupplierWithPurchase
 * @package Stokq\DTO
 */
class SupplierWithPurchase
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $website;

    /**
     * @var string
     */
    private $note;

    /**
     * @var string
     */
    private $purchaseCount;

    /**
     * @var string
     */
    private $totalTransaction;

    /**
     * @var string
     */
    private $lastOrder;

    /**
     * @param string $id
     * @param string $name
     * @param string $address
     * @param string $contact
     * @param string $email
     * @param string $website
     * @param string $note
     * @param string $purchaseCount
     * @param string $totalTransaction
     * @param string $lastOrder
     */
    function __construct($id, $name, $address, $contact, $email, $website, $note, $purchaseCount, $totalTransaction, $lastOrder)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->contact = $contact;
        $this->email = $email;
        $this->website = $website;
        $this->note = $note;
        $this->purchaseCount = $purchaseCount;
        $this->totalTransaction = $totalTransaction;
        $this->lastOrder = $lastOrder;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * @param string $contact
     */
    public function setContact($contact)
    {
        $this->contact = $contact;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
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
     * @return string
     */
    public function getPurchaseCount()
    {
        return $this->purchaseCount;
    }

    /**
     * @param string $purchaseCount
     */
    public function setPurchaseCount($purchaseCount)
    {
        $this->purchaseCount = $purchaseCount;
    }

    /**
     * @return string
     */
    public function getTotalTransaction()
    {
        return $this->totalTransaction;
    }

    /**
     * @param string $totalTransaction
     */
    public function setTotalTransaction($totalTransaction)
    {
        $this->totalTransaction = $totalTransaction;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getLastOrder()
    {
        return $this->lastOrder;
    }

    /**
     * @return string
     */
    public function getFormattedLastOrder()
    {
        $lastOrder = $this->getLastOrder();
        if ($lastOrder instanceof \DateTime) {
            return $lastOrder->format('M d, Y');
        }

        $dt = \DateTime::createFromFormat('U', strtotime($lastOrder));
        if ($dt instanceof \DateTime) {
            return $dt->format('M d, Y');
        }

        return null;
    }

    /**
     * @param string $lastOrder
     */
    public function setLastOrder($lastOrder)
    {
        $this->lastOrder = $lastOrder;
    }

}